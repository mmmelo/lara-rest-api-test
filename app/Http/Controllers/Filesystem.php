<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Group;
use App\Media;
use App\Schedules;
use App\Sports_type;
use App\States;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;



class Filesystem extends Controller
{
    public function __construct()
    {
        $this->groups = new Groups();
        $this->schedules = new Schedules();
        $this->media_files = new MediaFiles();
    }
    public function index()
    {
//        $data = Custom::get_schedule_season();

        $states = States::all();

        $sports_types = Sports_type::whereBetween('user_type_id',[101,199])->get();


        return view('dropzone')
            ->with('sports',$sports_types)
//            ->with('data',$data)
            ->with('states',$states);
    }

    public function file_upload(Request $request)
    {

//        //Getting the schedule information from the School
//        $school = $request->get('school');
//
//        // File construction information//
//
//        $upload_file = $request->file('file');
//        $file_name= $this->createFilename($upload_file);
//
//        //Thumbnail construction
//
//        $thumbnail = $this->small_picture($upload_file);
//        $thumbnail_file = $thumbnail['content'];
//        $thumbnail_uri = $thumbnail['url'];
//
//
//        //Saving the Temp Thumbnail file into dropbox
//        Storage::disk('dropbox')->putFileAs("2018-2019/".$school,new File($thumbnail_uri),"thum_".$file_name);
//
//        //Destroying the temporary file thumbnail
//        $thumbnail_file->destroy();
//        unlink($thumbnail_uri);
//
//        //Saving the full resolution if the image
//        Storage::disk('dropbox')->putFileAs("2018-2019/".$school,$upload_file,$file_name);


        return response()->json($request);


    }

    public function dropzone(Request $request)
    {



        //Create the file receiver
        $receiver = new FileReceiver("file",$request,HandlerFactory::classFromRequest($request));

        //Check if the upload is success , throw exception or return response you need
        if($receiver->isUploaded()=== false){
            throw new UploadMissingFileException();
        }

        //receive the file

        $save  = $receiver->receive();

        //Check if the upload has finished
        if ($save->isFinished())
        {
            //save the file in the cloud drop box or google cloud and also record the database
            return $this->saveFiletoCloud(
                $request,
                $save->getFile());
        }

        //Because we are on chunk mode we need to send the status
        //

        $handler = $save->handler();

        $school = $request->get('school_name');


        return response() ->json([
            "done"=> $handler->getPercentageDone(),
            'status' => true,
            'school_key'=>$request->get('school_key'),
            'school_name '=>$school,
            'disk'=>$request->get('disk_selector'),
            'watermark'=>$request->get('watermark')
        ]);

    }

    //This function is saving the File LOCAL if is needed
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        // Group files by the date (week
        $dateFolder = date("Y-m-W");
        // Build the file path
//        $filePath = "upload/{$mime}/{$dateFolder}/";
//        $finalPath = storage_path("app/".$filePath);
        $finalPath = public_path().('/upload/');
        // move the file name
        $file->move($finalPath, $fileName);
        return response()->json([
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension
        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;
        return $filename;
    }

    /**
     * @param $request
     * @param $file
     * @return \Illuminate\Http\JsonResponse
     *
     * This function would save the information is the cloud dropbox or google cloud services
     * also modify record inside the database
     */
    protected function saveFiletoCloud($request,$file)
    {

        $id = $request->get('school_key');
        $drive = $request->get('disk_selector');
        $watermark = $request->get('watermark');


        //Composing the fine name
        $filename = $this->createFilename($file);
        //Thumbnail construction

        $thumbnail = $this->small_picture($file,$filename,$watermark);
        $thumbnail_name = $thumbnail['name'];
        $thumbnail_file = $thumbnail['content'];
        $thumbnail_uri = $thumbnail['url'];

        //Selecting the type of Disk

        switch ($drive)
        {
            case "google":
                $disk = Storage::disk('gcs');
                $folder = '';
                $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $media_url = $disk->url($folder.$filename);
                break;
            case "dropbox":
                $disk = Storage::disk('dropbox');
                $folder = '/'.$request->get('school_name');
                $bucket = 'DROPBOX';
                $media_url = 'N/A';
                break;
        }

        //Saving the Temp Thumbnail file into drop_box


        $disk->putFileAs($folder,new File($thumbnail_uri),$thumbnail_name,'public');

        //Here we are saving the file inside the Drop_box
        $disk->putFileAs($folder,$file,$filename,'public');

        //We need to unlink the file when uploaded to dropbox
        unlink($file->getPathname());
        $thumbnail_file->destroy();
        unlink($thumbnail_uri);

        //UPDATING THE DATA RECORD on tables

        $records = Schedules::where('id',$id)->update(['bulk_load'=>1]);

        //Constructing the Media file//

        $media_file =
            [
                'name' => $filename,
                'bucket' => $bucket,
                'content_type' => 'image/jpg',
                'media' => $media_url,
                'ext'=>'jpg'
            ];

        $this->save_data_DB($id,$media_file);

        return response()->json([
            $media_file
    ]);
    }

    protected function save_data_DB($id,$media_file)
    {

        $data = $this->schedules->find($id);
        $groups_ids = ['home_id' => $data['group_id'],'opponent_id'=>$data['event_opponent_id']];


        $files = $this->media_files->saveMediafile($media_file);


        foreach ($groups_ids as $group_id)
        {
            $this->groups->setMediaFiles($group_id,$files,$id);
        }

        return $groups_ids;
    }


    protected function small_picture($file,$filename,$watermark = 'yes')
        /** This private function would create a small version of the File
         *  and add the water mark to it.
         * The resolution of the file would be set to 300 px constraint .
         * @Param is the input file $file
         * @Param $filename is the name of the file created
         * @Param $watermark is the option send in order to save or not the file with watermark default is yes
         * @Return is the content file and the url where the picture is saved inside the local server
         */

    {
        $chunk_file = $file->getPathname();
        $chunk_path = $file->getPath();//This version would save the file but hidden
        $chunk_path_normalized = str_replace("/.","/",$chunk_path);

        //refactoring the image
        $resize = Image::make(imagecreatefromjpeg($chunk_file));
        $resize->resize(300,null,function($constraint)
        {
            $constraint->aspectRatio();
        });
        $resize->encode('jpg');

        if($watermark == "yes")
        {
            //Here we are collecting and adding the Watermark image
            $watermark = Image::make(storage_path('app/watermark/my_gsnp_watermark.png'));
            $resize->insert($watermark,'center');
        }

        //Collecting original name and url of the image saved temp in the server
        $thumbnail_image_name = "thum_".$filename;
        $resize->save($chunk_path_normalized.$thumbnail_image_name);
        $saved_image_uri = $resize->dirname."/".$resize->basename;

        //Sending back the content and the url of the thumbnail
        return array(
            'content'=>$resize,
            'name'=>$resize->basename,
            'url' => $saved_image_uri);
    }
}
