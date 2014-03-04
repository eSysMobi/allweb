<?php

/**
 * This is the model class for table "resumes".
 *
 * The followings are the available columns in table 'resumes':
 * @property integer $id
 * @property string $site
 * @property string $name
 * @property string $job
 * @property string $date
 * @property string $description
 * @property string $phone
 * @property string $email
 * @property string $salary
 * @property string $link
 * @property integer $called
 * @property integer $deleted
 * @property string $parsing_date
 * @property string $offer_company
 * @property string $offer_phone
 * @property string $offer_comment
 */
class Resumes extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'resumes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('called, deleted', 'numerical', 'integerOnly'=>true),
			array('site, phone, email, salary', 'length', 'max'=>100),
			array('name, job, date, description, link, offer_company, offer_phone, offer_comment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, site, name, job, date, description, phone, email, salary, link, called, deleted, parsing_date, offer_company, offer_phone, offer_comment', 'safe', 'on'=>'search'),
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
			'site' => 'Site',
			'name' => 'Name',
			'job' => 'Job',
			'date' => 'Date',
			'description' => 'Description',
			'phone' => 'Phone',
			'email' => 'Email',
			'salary' => 'Salary',
			'link' => 'Link',
			'called' => 'Called',
			'deleted' => 'Deleted',
			'parsing_date' => 'Parsing Date',
			'offer_company' => 'Offer Company',
			'offer_phone' => 'Offer Phone',
			'offer_comment' => 'Offer Comment',
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
		$criteria->compare('site',$this->site,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('job',$this->job,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('salary',$this->salary,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('called',$this->called);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('parsing_date',$this->parsing_date,true);
		$criteria->compare('offer_company',$this->offer_company,true);
		$criteria->compare('offer_phone',$this->offer_phone,true);
		$criteria->compare('offer_comment',$this->offer_comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Resumes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
