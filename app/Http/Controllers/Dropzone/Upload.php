<?php

namespace App\Http\Controllers\Dropzone;


use App\Media;
use App\Http\Controllers\MediaFiles;
use App\Http\Controllers\NewsArticle\ArticleMedia;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Intervention\Image\Facades\Image;

class Upload extends ApiController
{
    public function __construct() {
        $this->media_files = new MediaFiles();
        $this->article_media = new ArticleMedia();
    }
    /** This Drop Zone function would handel all the upload to the system
     *
     * Method availables:
     * 1.- Dropzone used on the Article creation
     */
    public function dropzone(Request $request)
    {
        /** Request Inputs
        * @Params:
         *@Request_type: Here you need to specify the type of event you are requesting
         *(Request Types available : article)
         *@
        */


        //--------------------------General Procedure File Creation--------------------//

        //Create the file receiver
        $receiver = new FileReceiver("file",$request,HandlerFactory::classFromRequest($request));

        //Check if the upload is success , throw exception or return response you need
        if($receiver->isUploaded()=== false){
            throw new UploadMissingFileException();
        }

        //receive the file

        $save  = $receiver->receive();


        //--------------------------General Procedure End----------------------------//

        //Check if the upload has finished



        if ($save->isFinished())
        {
            //save the file in the cloud drop box or google cloud and also record the database

            return $this->saveFiletoCloud(
                $request,
                $save->getFile());
        }

        //Because we are on chunk mode we need to send the status//
        $handler = $save->handler();


        return response() ->json([
            "done"=> $handler->getPercentageDone(),
            'status' => true
        ]);

    }


    private function saveFiletoCloud($request,$file)
    {
        //--------------GENERAL VARIABLES AND SETUP------------------//
        $request_type = $request->get('request_type');  //Selecting type of Request
        $watermark = $request->get('watermark');        //Selecting if the requesting thumbnail with watermark
        $drive = $request->get('disk_selector');        //Selecting the Drive to save

        //-------------------Creation of the File Name ------------//

        //Composing the fine name
        $filename = $this->createFilename($file);

        //Thumbnail construction

        $thumbnail = $this->small_picture($file,$filename,$watermark);
        $thumbnail_name = $thumbnail['name'];
        $thumbnail_file = $thumbnail['content'];
        $thumbnail_uri = $thumbnail['url'];


        //----------Reacting depend of th type request ------------//
        switch ($request_type)
        {
            case "article":
                $article_id = $request->get('article_id');
                $disk = $this->selectionOfDrive($drive,$filename,$request_type);
                $this->savingTempAndFinalFiles(
                    $disk['disk'],
                    $disk['folder'],
                    $thumbnail_uri,
                    $thumbnail_name,
                    $thumbnail_file,
                    $file,
                    $filename);

                //Constructing the Media file//
                $media_file =
                    [
                        'name' => $filename,
                        'bucket' => $disk['bucket'],
                        'content_type' => 'image/jpg',
                        'media' => $disk['media_url'],
                        'ext'=>'jpg'
                    ];
                $this->save_data_article($article_id,$media_file);
                break;
        }



        return $this->printData('Upload Completed...');
    }

    private function save_data_article($articleId,$media_file)
    {
        $files = $this->media_files->saveMediafile($media_file);

        $this->article_media->AddMediaToArticle($articleId,$files);

    }

    private function selectionOfDrive($drive, $filename, $request)
    {
        switch ($drive)
        {
            case "google":
                $disk = Storage::disk('gcs');
                $folder = '';
                $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $media_url = $disk->url($folder.$filename);
                return array(
                    'disk'=>$disk,
                    'bucket'=>$bucket,
                    'folder'=>$folder,
                    'media_url'=>$media_url);

            case "dropbox":
                $disk = Storage::disk('dropbox');
                $folder = '/'.$request->get('school_name');
                $bucket = 'DROPBOX';
                $media_url = 'N/A';
                return array(
                    'disk'=>$disk,
                    'bucket'=>$bucket,
                    'folder'=>$folder,
                    'media_url'=>$media_url);
        }
    }

    private function savingTempAndFinalFiles($disk, $folder, $thumbnail_uri, $thumbnail_name, $thumbnail_file, $file, $filename)
    {
        //Saving the Temp Thumbnail file

        $disk->putFileAs($folder,new File($thumbnail_uri),$thumbnail_name,'public');

        //Here we are saving the file
        $disk->putFileAs($folder,$file,$filename,'public');

        //We need to unlink the file when uploaded
        unlink($file->getPathname());
        $thumbnail_file->destroy();
        unlink($thumbnail_uri);
    }

    private function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension
        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;
        return $filename;
    }

    private function save_data_DB($id,$media_file)
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
    private function small_picture($file,$filename,$watermark = 'yes')
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
