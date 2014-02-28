<?php

/**
 * This is the model class for table "avito_resume".
 *
 * The followings are the available columns in table 'avito_resume':
 * @property integer $id
 * @property string $salary
 * @property string $name
 * @property string $job
 * @property string $date
 * @property string $city
 * @property string $phone
 * @property string $description
 * @property string $category
 * @property integer $type
 * @property string $link
 */
class AvitoResume extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'avito_resume';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('salary, name, job, date, city, phone, description, category, type, link', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('salary', 'length', 'max'=>10),
			array('name, phone', 'length', 'max'=>20),
			array('city', 'length', 'max'=>30),
			array('category', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, salary, name, job, date, city, phone, description, category, type, link', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'salary' => 'Salary',
			'name' => 'Name',
			'job' => 'Job',
			'date' => 'Date',
			'city' => 'City',
			'phone' => 'Phone',
			'description' => 'Description',
			'category' => 'Category',
			'type' => 'Type',
			'link' => 'Link',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('salary',$this->salary,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('job',$this->job,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('link',$this->link,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AvitoResume the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public function get_search_page($num) {
		Yii::import('application.components.AvitoSearchResults');
		$avito = new AvitoSearchResults();
		$avito->get_results_from_page(1);
		
	}
}
