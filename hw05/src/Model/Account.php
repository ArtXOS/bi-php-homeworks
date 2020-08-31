<?php

namespace App\Model;

use App\Db;

class Account
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $number;

    /** @var string */
    protected $code;

    /**
     * Account constructor.
     *
     * @param int    $id
     * @param string $number
     * @param string $code
     */
    public function __construct(int $id, string $number, string $code)
    {
        $this->id = $id;
        $this->number = $number;
        $this->code = $code;
    }

    /**
     * Creates DB table using CREATE TABLE ...
     */
    public static function createTable(): void
    {
        $db = Db::get();
        $db->query('CREATE TABLE IF NOT EXISTS `account`(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    number VARCHAR[10],
    code VARCHAR[10]);');
    }

    /**
     * Drops DB table using DROP TABLE ...
     */
    public static function dropTable(): void
    {
        $db = Db::get();
        $db->query('DROP TABLE IF EXISTS `account`;');
    }

    /**     * Find account record by number and bank code
     *
     * @param string $number
     * @param string $code
     * @return Account|null
     */
    public static function find(string $number, string $code): ?self
    {
        $db = DB::get();
        $stmt = $db->prepare("SELECT account.id,account.number,account.code FROM account WHERE account.number = ? AND account.code = ?;");
        $stmt->execute([$number, $code]);
        $data = $stmt->fetch();

        if($data === false) {
            return null;
        } else {
            $account = new Account($data['id'], $data['number'], $data['code']);
            return $account;
        }
    }

    /**
     * Inserts new account record and returns its instance; or returns existing account instance
     *
     * @param string $number
     * @param string $code
     * @return static
     */
    public static function findOrCreate(string $number, string $code): self
    {
        $db = Db::get();
        $account = Account::find($number, $code);

        if($account === NULL) {
            $stmt = $db->prepare("INSERT INTO account (number,code) VALUES (?, ?);");
            $stmt->execute([$number,$code]);
            return Account::find($number, $code);
        } else {
            return $account;
        }
    }

    /**
     * Finds account by id and returns its instance
     * @param int $id account id
     * @return static|null
     */
    public static function findById(int $id) : ?self {

        $db = Db::get();
        $stmt = $db->prepare("SELECT * FROM account WHERE account.id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if($result == false) {
            return null;
        }

        return new Account($result['id'], $result['number'],$result['code']);
    }

    /**
     * Returns array of Transaction instances related to this Account, consider both transaction direction
     *
     * @return Transaction[]|array
     */
    public function getTransactions(): array
    {
        $array = array();
        $db = Db::get();

        $stmt = $db->prepare("SELECT * FROM 'transaction' WHERE 'transaction'.'from' = ? OR 'transaction'.'to' = ?;");
        $stmt->execute([$this->id, $this->id]);
        $data = $stmt->fetchAll();

        foreach ($data as $row) {
            $fromAccount = Account::findById($row['from']);
            $toAccount = Account::findById($row['to']);
            $array[] = new Transaction($fromAccount, $toAccount, $row['amount']);

        }
        return $array;
    }

    /**
     * Returns transaction sum (using SQL aggregate function)
     *
     * @return float
     */
    public function getTransactionSum(): float
    {
        $result = $this->getTransactions();
        $sum = 0;

        foreach($result as $transaction) {
            $transaction->getFrom()->getId() == $this->getId() ? $sum -= $transaction->getAmount() : $sum += $transaction->getAmount();
        }
        return $sum;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Account
     */
    public function setId(int $id): Account
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Account
     */
    public function setNumber(string $number): Account
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Account
     */
    public function setCode(string $code): Account
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Account string representation
     *
     * @return string
     */
    public function __toString()
    {
        return "{$this->number}/{$this->code}";
    }
}
