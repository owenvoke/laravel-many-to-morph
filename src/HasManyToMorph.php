<?php

namespace Nevadskiy\ManyToMorph;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Str;

/**
 * @mixin Model
 */
trait HasManyToMorph
{
	protected function manyToMorph(
		string $morphName,
		string|null $table = null,
		string|null $morphTypeColumn = null,
		string|null $morphKeyColumn = null,
		string|null $foreignKeyColumn = null,
		string|null $parentKeyColumn = null
	): ManyToMorph {
		$table = $table ?? Str::plural($morphName);

		$morphTypeColumn = $morphTypeColumn ?? "{$morphName}_type";

		$morphKeyColumn = $morphKeyColumn ?? "{$morphName}_id";

		$foreignKeyColumn = $foreignKeyColumn ?? $this->getForeignKey();

		$parentKeyColumn = $parentKeyColumn ?? $this->getKeyName();

		return $this->newManyToMorph($table, $morphTypeColumn, $morphKeyColumn, $foreignKeyColumn, $parentKeyColumn);
	}

	protected function newManyToMorph(
		string $table,
		string $morphTypeColumn,
		string $morphKeyColumn,
		string $foreignKeyColumn,
		string $parentKeyColumn
	): ManyToMorph {
		if (is_subclass_of($table, Model::class)) {
			$pivot = new $table();
		} else {
			$pivot = new MorphPivot();

			$pivot->setConnection($this->getConnection()->getName());

			$pivot->setTable($table);

			$pivot->timestamps = false;
		}

		return new ManyToMorph(
			$this,
			$pivot,
			$foreignKeyColumn,
			$morphTypeColumn,
			$morphKeyColumn,
			$parentKeyColumn,
		);
	}
}
