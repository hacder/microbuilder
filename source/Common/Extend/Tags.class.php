<?php
namespace Common\Extend;
use Think\Template\TagLib;

class Tags extends TagLib {
    protected $tags = array(
        'daterange'     =>      array('attr' => array('name', 'start', 'end'), 'close' => '0')
    );

    public function  _daterange($attr, $content) {
        $s = '';
        if (!defined('TAG_INIT_DATERANGE')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange").each(function(){
                            var elm = this;
                            var withTime = $(elm).attr("data-time").toLowerCase() == "true";
                            $(this).daterangepicker({
                                format: "YYYY-MM-DD",
                                startDate: $(elm).prev().prev().val(),
                                endDate: $(elm).prev().val(),
                                timePicker: withTime,
                                timePickerIncrement: 1,
                                timePicker12Hour: false
                            }, function(start, end){
                                var format = "YYYY-MM-DD";
                                if(withTime) {
                                    format = "YYYY-MM-DD HH:mm"
                                }
                                $(elm).find(".date-title").html(start.format(format) + " 至 " + end.format(format));
                                $(elm).prev().prev().val(start.format(format));
                                $(elm).prev().val(end.format(format));
                            });
                        });
                    });
                });
            </script>';
            define('TAG_INIT_DATERANGE', true);
        }

        $val = $attr;
        if(empty($val['time'])) {
            $val['time'] = 'false';
        }

        $s .= '
        <input name="'.$val['name'].'[start]" type="hidden" value="'. $val['start'].'" />
        <input name="'.$val['name'].'[end]" type="hidden" value="'. $val['end'].'" />
        <button class="btn btn-default daterange" data-time="'.$val['time'].'" type="button"><span class="date-title">'.$val['start'].' 至 '.$val['end'].'</span> <i class="fa fa-calendar"></i></button>
        ';
        return $s;
    }
}
