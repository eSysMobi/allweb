<?php
/* @var $this AvitoResumeController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Avito Resumes',
);

$this->menu=array(
	array('label'=>'Create AvitoResume', 'url'=>array('create')),
	array('label'=>'Manage AvitoResume', 'url'=>array('admin')),
);
?>

<h1>Avito Resumes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
