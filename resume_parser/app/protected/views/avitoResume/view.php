<?php
/* @var $this AvitoResumeController */
/* @var $model AvitoResume */

$this->breadcrumbs=array(
	'Avito Resumes'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List AvitoResume', 'url'=>array('index')),
	array('label'=>'Create AvitoResume', 'url'=>array('create')),
	array('label'=>'Update AvitoResume', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AvitoResume', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AvitoResume', 'url'=>array('admin')),
);
?>

<h1>View AvitoResume #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'salary',
		'name',
		'job',
		'date',
		'city',
		'phone',
		'description',
		'category',
		'type',
		'link',
	),
)); ?>
