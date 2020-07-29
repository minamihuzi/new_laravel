<div role="dialog"  class="modal fade" style="display: none;">
    @include('ManageOrganiser.Partials.EventCreateAndEditJS');

    {!! Form::open(array('url' => route('postCreateHtmlSign'), 'class' => 'ajax gf')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-calendar"></i>
                    @lang("basic.html_title")</h3>
            </div>
            <div class="modal-body">
                <div class="row" style="border:1px solid #525996;; width:100%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
                    <table width="100%">
                        <tr><td style="padding:20px;text-align: left; font-size: 20px;">
                                <style>
                                    #signatureparent,#signatureparent1{
                                        color: darkblue;
                                        background-color: #FFF;
                                        padding: 20px;
                                    }
                                    #signature, #signature1 {
                                        border: 2px dotted black;
                                        background-color:lightgrey;
                                    }
                                    html.touch #content {
                                        float:left;
                                        width:92%;
                                    }
                                    html.touch #scrollgrabber {
                                        float:right;
                                        width:4%;
                                        margin-right:2%;
                                        background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAFCAAAAACh79lDAAAAAXNSR0IArs4c6QAAABJJREFUCB1jmMmQxjCT4T/DfwAPLgOXlrt3IwAAAABJRU5ErkJggg==)
                                    }
                                    html.borderradius #scrollgrabber {
                                        border-radius: 1em;
                                    }
                                </style>
                            <!--<script src="{{asset('assets/javascript/libs/jquery.js')}}"></script>-->
                                <script>
                                    /*  @preserve
                                    jQuery pub/sub plugin by Peter Higgins (dante@dojotoolkit.org)
                                    Loosely based on Dojo publish/subscribe API, limited in scope. Rewritten blindly.
                                    Original is (c) Dojo Foundation 2004-2010. Released under either AFL or new BSD, see:
                                    http://dojofoundation.org/license for more information.
                                    */
                                    (function($) {
                                        var topics = {};
                                        $.publish = function(topic, args) {
                                            if (topics[topic]) {
                                                var currentTopic = topics[topic],
                                                    args = args || {};

                                                for (var i = 0, j = currentTopic.length; i < j; i++) {
                                                    currentTopic[i].call($, args);
                                                }
                                            }
                                        };
                                        $.subscribe = function(topic, callback) {
                                            if (!topics[topic]) {
                                                topics[topic] = [];
                                            }
                                            topics[topic].push(callback);
                                            return {
                                                "topic": topic,
                                                "callback": callback
                                            };
                                        };
                                        $.unsubscribe = function(handle) {
                                            var topic = handle.topic;
                                            if (topics[topic]) {
                                                var currentTopic = topics[topic];

                                                for (var i = 0, j = currentTopic.length; i < j; i++) {
                                                    if (currentTopic[i] === handle.callback) {
                                                        currentTopic.splice(i, 1);
                                                    }
                                                }
                                            }
                                        };
                                    })(jQuery);

                                </script>
                                <script src="{{asset('assets/javascript/src/jSignature.js')}}"></script>
                                <script src="{{asset('assets/javascript/src/plugins/jSignature.CompressorBase30.js')}}"></script>
                                <script src="{{asset('assets/javascript/src/plugins/jSignature.CompressorSVG.js')}}"></script>
                                <script src="{{asset('assets/javascript/src/plugins/jSignature.UndoButton.js')}}"></script>
                                <script src="{{asset('assets/javascript/src/plugins/signhere/jSignature.SignHere.js')}}"></script>
                                <script>
                                    function setPdfSigner() {
                                        $("#signature canvas").trigger("resize");
                                    }
                                    function setSignature(){
                                        var $extraarea = $('#displayarea');
                                        $extraarea.html("");
                                        var i = new Image()
                                        var data = $("#signature").jSignature('getData', "image");
                                        i.src = 'data:' + data[0] + ',' + data[1]
                                        $('<span></span>').appendTo($extraarea)
                                        $(i).appendTo($extraarea)
                                    }
                                    $(document).ready(function() {
                                        // This is the part where jSignature is initialized.
                                        var $sigdiv = $("#signature").jSignature({'UndoButton':true})
                                        var $sigdiv1 = $("#signature1").jSignature({'UndoButton':true})
                                    })

                                </script>
                                <div>
                                    <div id="content">
                                        <div class="text-center"><h1>{{$sign_html->title}}</h1></div>
                                        <div class="col-md-12" style="margin-left:30px;margin-right:30px;">{{$sign_html->description}}</div>
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div id="signatureparent">
                                                    <div id="signature"></div>
                                                </div>
                                                <div class="text-center"><p>(Releasor's Signature)</p></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="signatureparent1">
                                                    <div id="signature1"></div>
                                                </div>
                                                <div class="text-center"><p>(Parent's Signature, if Signatory is minor)</p></div>
                                            </div>
                                            <div id="tools"></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="col-md-12">
                                                    <input type="text" id="sign_user_name" class="form-control">
                                                    <div class="text-center"><p>(Print Name)</p></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-12">
                                                    <input type="text" id="sign_parent_name" class="form-control">
                                                    <div class="text-center"><p>(Print Name)</p></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="col-md-12">
                                                    <input type="text" id="sign_date" class="form-control">
                                                    <div class="text-center"><p>(Date)</p></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                            </div>
                                        </div>
                                        <div><button class="btn" @click="closeConfirmSignModal">done signature:</button><div id="displayarea"></div></div>
                                    </div>
                                    <div id="scrollgrabber"></div>
                                </div>
                            </td></tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="uploadProgress"></span>
                {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit(trans("basic.create_html_sign"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
