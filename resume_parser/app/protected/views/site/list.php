<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name . ' - Список резюме';
$this->breadcrumbs=array(
	'Список резюме',
);
?>
<?
$imghtml=CHtml::image(Yii::app()->getBaseUrl(true).'/images/reload1.png','Обновить',array('class' => 'reload_button'));
echo CHtml::link($imghtml, '#',array('onclick' => "return false;"));
?>
<style type='text/css'>
 .more-data{ display:none}
</style>
<script type='text/javascript'>
$(document).on('click', '.readMore', function() {
    $(this).parent().find(".more-data").eq(0).toggle();
	if ($(this).text()=='Раскрыть') {
		$(this).text('Скрыть');
	} else {
		$(this).text('Раскрыть');
	}
	$(this).parent().find(".less-data").eq(0).toggle();
});
$(document).on('click', '.reload_button', function() {
	update_grid();
});
function update_grid() {
	$.fn.yiiGridView.update('yw0');
}

jQuery(document).on('click','#yw0 a.call',function() {
	var urls = $(this);
    var url = $(this).attr('href');
    $.get(url, function(response) {
		if(document.URL.indexOf("all=") == -1) {
			urls.parent().parent().parent().hide();
			return false;
		} else {
			if (urls.text()=='Звонили') {
				urls.text('Не звонили');
			} else {
				urls.text('Звонили');
			}
			return false;
		}
    });
    return false;
});
$(document).on('click', '#all', function() {
	var url = document.URL;
	if ($(this).attr('checked')) {
		url = url +'&all=1';
	} else {
		url = url.replace('&all=1', '');
		url = url.replace('all=1', '');
	}
	window.location.replace(url);
});
</script>
<h1>Список резюме</h1>
<?php
$js_preview =<<< EOD
function() {
	var urls = $(this);
    var url = $(this).attr('href');
    $.get(url, function(response) {
		console.log(urls); 
        urls.parent().parent().hide();
    });
    return false;
}
EOD;
?>
<form method="get" action="<?echo Yii::app()->createUrl("site/call");?>">
Фильтр<br />
Имя: <input type="text" name="name" value="<?echo (isset($vals['name'])?$vals['name']:'');?>">
Работа: <input type="text" name="job" value="<?echo (isset($vals['job'])?$vals['job']:'');?>">
Телефон/Email: <input type="text" name="contact" value="<?echo (isset($vals['contact'])?$vals['contact']:'');?>">
Сайт: <select name="site"><?
foreach(array('avito','sarbc','164ru','joblab','kariera','komushto','rdw','Все') as $site) {
	echo "<option value='{$site}' ";
	if (isset($vals['site']) && $site==$vals['site']) {
		echo 'selected';
	}
	echo ">{$site}</option>";
}
?>
</select>
<input type="checkbox" name="all" id="all" value="1" <?echo (isset($vals['all']) && $vals['all']==1?'checked':'');?>>Показывать все<br /><br />
<input type="hidden" name="r" value="site/list">
<input type="submit" value="Применить">
</form>
<?
mb_internal_encoding("UTF-8");
$this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'columns' => array(
			array('header'=>'Сайт', 'name' => 'site', 'value'=>'"<div class=\"hiddenid\">".$data->id."</div><a target=\"blank\" href=\"".$data->link."\">".$data->site."</a>"', 'type' => 'raw'),
			array('header'=>'Имя', 'name' => 'name', 'value'=>'$data->name."<div class=\"call_link\">".CHtml::link(($data->called?"Не звонили":"Звонили"),Yii::app()->createUrl("site/call",array("id"=>$data->id)),array("class" => "call"))."</div>"', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:70px')),
			array('header'=>'Работа', 'value'=>'$data->job'),
			array('header'=>'Контакты', 'name'=>'phone', 'value'=>'($data->phone?$data->phone." <br />":"").($data->email?"E-mail ".$data->email." <br />":"")', 'type' => 'raw'),
			array(
				'header' => 'Описание',
				'name' 	=> 'description',
				'value' => '"<div class=\"less-data\">".mb_substr($data->description,0,100)."...</div><div class=more-data>".$data->description."</div><a href=javascript:void(0); class=\"readMore\">Раскрыть</a>"',
				'type' 	=> 'raw',
			),
			array('header'=>'Зар. плата', 'value'=>'$data->salary', 'name' => 'salary'),
			array('header'=>'Дата', 'name' => 'date', 'value'=>'$data->date')
        ),
		'ajaxUpdate'=>true,
		'enableSorting'=>array('name','date','phone','site','salary'),

    ));
?>
