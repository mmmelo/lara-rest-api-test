<div class="panel panel-default collapse optional-panel">
    <div class="panel-heading">
        <h3 class="panel-title">Confirmation and Checkout</h3>
    </div>
    <div class="panel-body">
        <div class="row text-center">
            <div class="col-xs-12">
                <h3 ><strong><span id="total-approved"></span></strong> of {{$data->media->count()}}</h3>
                <h4>PICTURES APPROVED</h4>
            </div>
            <div class="col-xs-12">
                <button class="btn btn-danger">Cancel</button>
                <button id="commit" class="btn btn-success align-right">  Commit<span class="glyphicon glyphicon-log-out push-left"></span></button>
            </div>
        </div>
    </div>
</div>