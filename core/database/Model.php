<?php

namespace App\Core\Database;

abstract class Model {

    protected static string $table;

    public ?int $id = null;
    public ?string $deleted_at = null;

    /**
     * Start a new query for this model.
     * 
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder {
        return new QueryBuilder(static::class, static::$table);
    }

    /**
     * Shortcut for finding a single record by ID.
     * 
     * @param int $id
     * @return static|null
     */
    public static function find(int $id): ?static {
        return static::query()->where('id', '=', $id)->first();
    }

    public function save(): bool {
        $data = get_object_vars($this);
        $db = DB::connect();
        $now = date('Y-m-d H:i:s');

        if ($this->id) {
            // UPDATE logic
            unset($data['id']);
            if (array_key_exists('created_at', $data)) {
                unset($data['created_at']);
            }
            if (array_key_exists('updated_at', $data)) {
                $data['updated_at'] = $now;
                $this->updated_at = $now;
            }

            $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
            $sql = "UPDATE " . static::$table . " SET $fields WHERE id = {$this->id}";
            $params = array_values($data);
        } else {
            // INSERT logic
            unset($data['id']);
            if (array_key_exists('created_at', $data)) {
                $data['created_at'] = $now;
                $this->created_at = $now;
            }
            if (array_key_exists('updated_at', $data)) {
                $data['updated_at'] = $now;
                $this->updated_at = $now;
            }

            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";
            $params = array_values($data);
        }

        $stmt = $db->prepare($sql);
        $result = $stmt->execute($params);

        if ($result && !$this->id) {
            $this->id = (int)$db->lastInsertId();
        }

        return $result;
    }

    /**
     * Create a new instance from a database row.
     * This is converting raw data into model instances.
     * 
     * @param array $data
     * @return static
     */
    public static function hydrate(array $data): static {
        $instance = new static();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }
        return $instance;
    }

    /**
     * @return bool
     */
    public function delete(): bool {
        if (!$this->id) return false;
        $stmt = DB::connect()->prepare("UPDATE " . static::$table . " SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    /**
     * @return bool
     */
    public function restore(): bool {
        if (!$this->id) return false;
        $this->deleted_at = null;
        $stmt = DB::connect()->prepare(
            "UPDATE " . static::$table . " SET deleted_at = NULL WHERE id = ?"
        );
        return $stmt->execute([$this->id]);
    }

    /**
     * @return bool
     */
    public function forceDelete(): bool {
        if (!$this->id) return false;
        $stmt = DB::connect()->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}