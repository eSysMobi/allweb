<?php
/* @var $this AvitoResumeController */
/* @var $model AvitoResume */

$this->breadcrumbs=array(
	'Avito Resumes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List AvitoResume', 'url'=>array('index')),
	array('label'=>'Create AvitoResume', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#avito-resume-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Avito Resumes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'avito-resume-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'salary',
		'name',
		'job',
		'date',
		'city',
		/*
		'phone',
		'description',
		'category',
		'type',
		'link',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
