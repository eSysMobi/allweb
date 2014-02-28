<?php
/* @var $this AvitoResumeController */
/* @var $model AvitoResume */

$this->breadcrumbs=array(
	'Avito Resumes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AvitoResume', 'url'=>array('index')),
	array('label'=>'Manage AvitoResume', 'url'=>array('admin')),
);
?>

<h1>Create AvitoResume</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>