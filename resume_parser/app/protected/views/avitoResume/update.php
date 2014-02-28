<?php
/* @var $this AvitoResumeController */
/* @var $model AvitoResume */

$this->breadcrumbs=array(
	'Avito Resumes'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AvitoResume', 'url'=>array('index')),
	array('label'=>'Create AvitoResume', 'url'=>array('create')),
	array('label'=>'View AvitoResume', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AvitoResume', 'url'=>array('admin')),
);
?>

<h1>Update AvitoResume <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>