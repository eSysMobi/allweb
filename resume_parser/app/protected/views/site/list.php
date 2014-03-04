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
$(document).on('click', '.update_button', function() {
	
	var url = 'http://www.saratov-rabota.com:8081/resume_parser/app/index.php?r=site/update';
	var tr = $(this).parent().parent();
	var id = tr.find('div.hiddenid').html();
	if (id) {
		url += '&id='+id;
	}
	var val;
	var vars = ["offer_company", "offer_phone", "offer_comment"];
	vars.forEach(function(entry) {
		if (val = tr.find('.'+entry).attr('value')) {
			url += '&'+entry+'='+val;
		}
	});
	$.getJSON(url, 
    function(data) {
		if (data.status=='ok') {
			alert('Информация обновлена');
		} else {
			alert('Информация не обновлена');
		}
	});
	
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
			array('header'=>'Сайт', 'name' => 'site', 'value'=>'"<div class=\"hiddenid\">".$data->id."</div><a target=\"blank\" href=\"".$data->link."\">".$data->site."</a>"', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:40px')),
			array('header'=>'Имя', 'name' => 'name', 'value'=>'$data->name."<div class=\"call_link\">".CHtml::link(($data->called?"Не звонили":"Звонили"),Yii::app()->createUrl("site/call",array("id"=>$data->id)),array("class" => "call"))."</div>"', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:70px')),
			array('header'=>'Работа', 'value'=>'$data->job', 'htmlOptions'=>array('style'=>'width:150px')),
			array('header'=>'Контакты', 'name'=>'phone', 'value'=>'($data->phone?$data->phone." <br />":"").($data->email?"E-mail ".$data->email." <br />":"")', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:85px')),
			array(
				'header' => 'Описание',
				'name' 	=> 'description',
				'value' => '"<div class=\"less-data\">".mb_substr($data->description,0,100)."...</div><div class=more-data>".$data->description."</div><a href=javascript:void(0); class=\"readMore\">Раскрыть</a>"',
				'type' 	=> 'raw',
				'htmlOptions'=>array('style'=>'width:400px')
			),
			array('header'=>'Зар. плата', 'value'=>'$data->salary', 'name' => 'salary', 'htmlOptions'=>array('style'=>'width:70px')),
			array('header'=>'Дата', 'name' => 'date', 'value'=>'$data->date', 'htmlOptions'=>array('style'=>'width:150px')),
			array('header'=>'Компания', 'name' => 'offer_company', 'value'=>'CHtml::textField("",$data->offer_company,array("class" => "offer_company"))', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:150px')),
			array('header'=>'Телефон', 'name' => 'offer_phone', 'value'=>'CHtml::textField("",$data->offer_phone,array("class" => "offer_phone"))', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:150px')),
			array('header'=>'Комментарий', 'name' => 'offer_comment', 'value'=>'CHtml::textArea("",$data->offer_comment,array("class" => "offer_comment"))', 'type' => 'raw', 'htmlOptions'=>array('style'=>'width:150px')),
			array('header'=>' ','name' => 'update','value' => 'CHtml::button("Обновить", array("class" => "update_button"))', 'type' => 'raw') 
		),
		'ajaxUpdate'=>true,
		'enableSorting'=>array('name','date','phone','site','salary','offer_company','offer_phone','offer_comment'),

    ));
?>
