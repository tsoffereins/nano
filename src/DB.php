<?php namespace Nano;

use PDO;

class DB
{
	/**
	 * The database connection.
	 *
	 * @var PDO
	 */
	private $db;

	/**
	 * Setup a new database connection.
	 *
	 * @param  string $host
	 * @param  string $db
	 * @param  string $username
	 * @param  string $password
	 * @return void
	 */
	public function __construct(string $host, string $db, string $username = 'root', string $password = '')
	{
		$this->db = new PDO(
			"mysql:host=$host;dbname=$db", $username, $password
		);
	}

	/**
	 * Query the database.
	 *
	 * @param  string $sql
	 * @return mixed
	 */
	public function query($sql)
	{
		return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}
}