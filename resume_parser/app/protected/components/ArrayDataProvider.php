<?
class ArrayDataProvider extends CArrayDataProvider
{
	protected function fetchData()
	{
		if(($sort=$this->getSort())!==false && ($order=$sort->getOrderBy())!='')
			$this->sortData($this->getSortDirections($order));

		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			return $this->rawData;
		}
		else
			return $this->rawData;
	}
}
