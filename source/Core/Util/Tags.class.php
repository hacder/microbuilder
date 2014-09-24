<?php
namespace Core\Util;
use Core\Model\Member;
use Think\Template\TagLib;

class Tags extends TagLib {
    protected $tags = array(
        'daterange'     =>      array('attr' => array('name', 'start', 'end'), 'close' => '0'),
        'credits'        =>      array('attr' => array('name', 'scope', 'value'), 'close' => '0')
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

    public function _credits($attr, $content) {
        Member::loadSettings();
        $setting = C('MS');
        $credits = $setting[Member::OPT_CREDITS];
        $ds = array();
        if(!in_array($attr['scope'], array('enabled', 'disabled', 'all'))) {
            $attr['scope'] = 'enabled';
        }
        foreach($credits as $row) {
            if(!empty($row['enabled'])) {
                if($attr['scope'] == 'enabled' || $attr['scope'] == 'all') {
                    $ds[] = $row;
                    continue;
                }
            } else {
                if($attr['scope'] == 'disabled' || $attr['scope'] == 'all') {
                    $ds[] = $row;
                    continue;
                }
            }
        }
        $s = '<select name="' . $attr['name'] . '" class="form-control"><option value="">请选择积分类型</option>';
        foreach($ds as $row) {
            $selected = '<?php echo ' . $attr['value'] . ' == "' . $row['name'] . '" ? " selected" : "" ?>';
            $s .= "<option value=\"{$row['name']}\"{$selected}>{$row['title']}</option>";
        }
        $s .= '</select>';
        return $s;
    }
}
