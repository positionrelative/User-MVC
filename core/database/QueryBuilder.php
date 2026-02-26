<?php

namespace App\Core\Database;

/**
 * A simple query builder for fetching records from the database.
 */
class QueryBuilder {

    private string $table;
    private string $modelClass;
    private array $where = [];
    private array $params = [];
    private bool $withTrashed = false;

    public function __construct(string $modelClass, string $table) {
        $this->modelClass = $modelClass;
        $this->table = $table;
    }

    /**
     * Adds a WHERE condition to the query
     * 
     * string $column - The column name
     * string $operator - The SQL operator (=, <, >, etc.)
     * mixed $value - The value to compare against
     * 
     * @return self
     */
    public function where(string $column, string $operator, mixed $value): self {
        $this->where[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!$this->withTrashed) {
            $sql .= empty($this->where) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }

        $stmt = DB::connect()->prepare($sql);
        $stmt->execute($this->params);
        $results = $stmt->fetchAll();

        return array_map(
            fn($data) => $this->modelClass::hydrate($data), 
            $results
        );
    }

    /**
     * Fetches the first record that matches the query conditions
     * 
     * @return object|null
     */
    public function first(): ?object {
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * @return self
     */
    public function withTrashed(): self {
        $this->withTrashed = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function exists(): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!$this->withTrashed) {
            $sql .= empty($this->where) ? " WHERE deleted_at IS NULL" : " AND deleted_at IS NULL";
        }

        $stmt = DB::connect()->prepare($sql);
        $stmt->execute($this->params);
        return (int)$stmt->fetchColumn() > 0;
    }
}