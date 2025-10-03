<?php declare(strict_types=1);

namespace App\Model;

use App\Db;

class Account
{
    public function __construct(
        protected int    $id,
        protected string $number,
        protected string $code
    )
    {
    }

    /**
     * Creates DB table using CREATE TABLE ...
     */
    public static function createTable(): void
    {
        $db = Db::get();
        $db->query('CREATE TABLE IF NOT EXISTS `accounts` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `number` VARCHAR(255) NOT NULL,
            `code` VARCHAR(255) NOT NULL
        )');
    }

    /**
     * Drops DB table using DROP TABLE ...
     */
    public static function dropTable(): void
    {
        $db = Db::get();
        $db->query('DROP TABLE IF EXISTS `accounts`');
    }

    /**
     * Find account record by number and bank code
     */
    public static function find(string $number, string $code): ?self
    {
        $db = Db::get();
        $statement = $db->prepare('SELECT * FROM `accounts` WHERE `number` = :number AND `code` = :code');
        $statement->execute(['number' => $number, 'code' => $code]);
        $result = $statement->fetch();
        if ($result === false) {
            return null;
        }
        return new self(
            $result['id'],
            $result['number'],
            $result['code']
        );
    }

    /**
     * Find account record by id
     */
    public static function findById(int $id): ?self
    {
        $db = Db::get();
        $statement = $db->prepare('SELECT * FROM `accounts` WHERE `id` = :id');
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();
        if ($result === false) {
            return null;
        }
        return new self(
            $result['id'],
            $result['number'],
            $result['code']
        );
    }

    /**
     * Inserts new account record and returns its instance; or returns existing account instance
     */
    public static function findOrCreate(string $number, string $code): self
    {
        $db = Db::get();
        $account = self::find($number, $code);
        if ($account === null) {
            $statement = $db->prepare('INSERT INTO `accounts` (`number`, `code`) VALUES (:number, :code)');
            $statement->execute(['number' => $number, 'code' => $code]);
            return self::find($number, $code);
        } else {
            return $account;
        }
    }

    /**
     * Returns iterable of Transaction instances related to this Account, consider both transaction direction
     *
     * @return iterable<Transaction>
     */
    public function getTransactions(): iterable
    {
        $db = Db::get();
        $statement = $db->prepare('SELECT * FROM `transactions` WHERE `account_from` = :id OR `account_to` = :id');
        $statement->execute(['id' => $this->id]);
        $result = $statement->fetchAll();
        $transactions = [];
        foreach ($result as $row) {
             $transactions[] = new Transaction(
                self::findById($row['account_from']),
                self::findById($row['account_to']),
                (float)$row['amount']
            );
        }
        return $transactions;
    }


    /**
     * Returns transaction sum (using SQL aggregate function). Treat outgoing transactions as 'minus' and incoming as 'plus'.
     */
    public function getTransactionSum(): float
    {
        $sum = 0;
        foreach ($this->getTransactions() as $transaction){
            if ($transaction->getFrom()->id === $this->getId()){
                $sum -= $transaction->getAmount();
            } else {
                $sum += $transaction->getAmount();
            }
        }
        return $sum;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Account
    {
        $this->id = $id;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): Account
    {
        $this->number = $number;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Account
    {
        $this->code = $code;
        return $this;
    }

    public function __toString(): string
    {
        return "{$this->number}/{$this->code}";
    }
}
