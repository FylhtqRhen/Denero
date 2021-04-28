<?php

/**
 * Class Item
 */
final class Item
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $status;

    /**
     * @var bool
     */
    private $changed = false;

    /**
     * @var PDO
     */
    private $connect;

    /**
     * @var mixed|null
     */
    private $raw;

    private $itInit = false;

    /**
     * Item constructor.
     * @param int $id
     * @param PDO $connect
     */
    public function __construct(int $id, PDO $connect)
    {
        $this->id = $id;
        $this->connect = $connect;
        $this->init();

    }


    /**
     * Метод для сохранения изменений
     */
    public function save(): bool
    {
        if ($this->changed) {
            $sql = 'UPDATE `objects` SET `name` = ? , `status` = ? WHERE `id` = ?';
            $statement = $this->connect->prepare($sql);
            $success = $statement->execute([
                $this->name,
                $this->status,
                $this->id
            ]);
            if ($success) {
                $this->raw = null;
            }
            return $success;
        }
        return false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        $list = get_object_vars($this);
        if ($list[$name]) {
            return $this->$name;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'name':
                if (is_string($value) && !empty($value)) {
                    $this->name = $value;
                    $this->changed = true;
                }
                break;
            case 'status':
                if (is_int($value) && !empty($value)) {
                    $this->status = $value;
                    $this->changed = true;
                }
                break;
        }
    }

    /**
     * Метод для инициализации и заполнения свойств класса
     */
    private function init(): void
    {
        if ($this->itInit) {
            return;
        }
        $sql = 'SELECT `name`, `status`  FROM `objects` WHERE `id` = ?';
        $query = $this->connect->prepare($sql);
        $query->execute([$this->id]);
        $result = $query->fetch();
        if ($result) {
            $this->name = $result['name'];
            $this->status = $result['status'];
        }
        $this->itInit = true;
    }
}
