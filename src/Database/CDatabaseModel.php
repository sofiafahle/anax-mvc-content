<?php

namespace Anax\Database;
 
/**
 * Base class for database models
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	
	/**
	 * Get the table name.
	 *
	 * @return string with the table name.
	 */
	public function getSource()
	{
		return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
	}
	
	/**
	 * Find and return specific.
	 *
	 * @return this
	 */
	public function find($id)
	{
		$this->db->select()
				 ->from($this->getSource())
				 ->where("id = ?");
	 
		$this->db->execute([$id]);
		return $this->db->fetchInto($this);
	}
	
	
	/**
	 * Find and return all.
	 *
	 * @return array
	 */
	public function findAll()
	{
		$this->db->select()
				 ->from($this->getSource());
	 
		$this->db->execute();
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	/**
	 * Find and return rows based on condition.
	 *
	 * @param string $column as key for condition
	 * @param string $value as value for condition.
	 *
	 * @return array
	 */
	public function findWhere($column, $value) {
		$this->db->select()
				 ->from($this->getSource())
				 ->where($column . ' = ?');
		
		$this->db->execute([$value]);
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	/**
	 * Find and return rows in specific order.
	 *
	 * @param string $column as key for condition
	 * @param string $order as order to sort in, ASC or DESC.
	 *
	 * @return array
	 */
	public function findAllOrder($column, $order) {
		$this->db->select()
				 ->from($this->getSource())
				 ->orderBy($column . ' ' . $order);
		
		$this->db->execute();
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	/**
	 * Get object properties.
	 *
	 * @return array with object properties.
	 */
	public function getProperties()
	{
		$properties = get_object_vars($this);
		unset($properties['di']);
		unset($properties['db']);
	 
		return $properties;
	}
	
	/**
	 * Set object properties.
	 *
	 * @param array $properties with properties to set.
	 *
	 * @return void
	 */
	public function setProperties($properties)
	{
		// Update object with incoming values, if any
		if (!empty($properties)) {
			foreach ($properties as $key => $val) {
				$this->$key = $val;
			}
		}
	}
	
	/**
	 * Save current object/row.
	 *
	 * @param array $values key/values to save or empty to use object properties.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function save($values = [])
	{
		$this->setProperties($values);
		$values = $this->getProperties();
	 
		if (isset($values['id'])) {
			return $this->update($values);
		} else {
			return $this->create($values);
		}
	}
	
	
	/**
	 * Create new row.
	 *
	 * @param array $values key/values to save.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function create($values)
	{
		$keys   = array_keys($values);
		$values = array_values($values);
	 
		$this->db->insert(
			$this->getSource(),
			$keys
		);
	 
		$res = $this->db->execute($values);
	 
		$this->id = $this->db->lastInsertId();
	 
		return $res;
	}
	
	/**
	 * Update row.
	 *
	 * @param array $values key/values to save.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function update($values)
	{
		// Its update, remove id
		unset($values['id']);
		
		$keys   = array_keys($values);
		$values = array_values($values);
	 
		// Use id as where-clause
		$values[] = $this->id;
	 
		$this->db->update(
			$this->getSource(),
			$keys,
			"id = ?"
		);
	 
		return $this->db->execute($values);
	}
	
	/**
	 * Delete row by id.
	 *
	 * @param integer $id to delete.
	 *
	 * @return boolean true or false if deleting went okey.
	 */
	public function delete($id)
	{
		$this->db->delete(
			$this->getSource(),
			'id = ?'
		);
	 
		return $this->db->execute([$id]);
	}
	
	/**
	 * Delete all rows with where condition.
	 *
	 * @param string $column as key for condition
	 * @param string $value as value for condition.
	 *
	 * @return boolean true or false if deleting went okey.
	 */
	public function deleteWhere($column, $value)
	{
		$this->db->delete(
			$this->getSource(),
			$column . ' = ?'
		);
	 
		return $this->db->execute([$value]);
	}
	
	/**
	 * Delete all rows with where condition.
	 *
	 * @param string $page to delete from.
	 *
	 * @return boolean true or false if deleting went okey.
	 */
	public function deleteAll()
	{
		$this->db->delete(
			$this->getSource()
		);
	 
		return $this->db->execute();
	}
	
	
	/**
	 * Build a select-query.
	 *
	 * @param string $columns which columns to select.
	 *
	 * @return $this
	 */
	public function query($columns = '*')
	{
		$this->db->select($columns)
				 ->from($this->getSource());
	 
		return $this;
	}
	
	/**
	 * Build the where part.
	 *
	 * @param string $condition for building the where part of the query.
	 *
	 * @return $this
	 */
	public function where($condition)
	{
		$this->db->where($condition);
	 
		return $this;
	}
	
	/**
	 * Build the where part.
	 *
	 * @param string $condition for building the where part of the query.
	 *
	 * @return $this
	 */
	public function andWhere($condition)
	{
		$this->db->andWhere($condition);
	 
		return $this;
	}
	
	/**
	 * Execute the query built.
	 *
	 * @param string $query custom query.
	 *
	 * @return $this
	 */
	public function execute($params = [])
	{
		$this->db->execute($this->db->getSQL(), $params);
		$this->db->setFetchModeClass(__CLASS__);
	 
		return $this->db->fetchAll();
	}
	
	/**
	 * Create a slug of a string, to be used as url.
	 *
	 * @param string $str the string to format as slug.
	 * @returns str the formatted slug. 
	 */
	function slugify($str) {
		$str = mb_strtolower(trim($str));
		$str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = trim(preg_replace('/-+/', '-', $str), '-');
		return $str;
	}
		 
}