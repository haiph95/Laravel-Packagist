<?php

namespace App\HaiPham\Meta;

trait MetaTrait
{
    /**
     * Gets all meta data
     * @return Collection
     */
    public function getAllMeta()
    {
        return new Collection($this->meta->lists('value', 'key'));
    }

    /**
     * Gets meta data
     * @param $key
     * @param bool $getObj
     * @return Collection
     */
    public function getMeta($key, $getObj = false)
    {
        $meta = $this->meta()
            ->where('key', $key)
            ->get();
        if ($getObj)
        {
            $collection = $meta;
        }
        else
        {
            $collection = new Collection();
            foreach ($meta as $m)
            {
                $collection->put($m->id, $m->value);
            }
        }
        return $collection->count() <= 1 ? $collection->first() : $collection;
    }

    /**
     * Updates meta data
     * @param $key
     * @param $newValue
     * @param bool $oldValue
     * @return mixed
     */
    public function updateMeta($key, $newValue, $oldValue = false)
    {
        $meta = $this->getMeta($key, true);
        if ($meta == null)
        {
            return $this->addMeta($key, $newValue);
        }
        $obj = $this->getEditableItem($meta, $oldValue);
        if ($obj !== false)
        {
            $isSaved = $obj->update([
                'value' => $newValue
            ]);
            return $isSaved ? $obj : $obj->getErrors();
        }
    }

    /**
     * Adds meta data
     * @param $key
     * @param $value
     * @return bool
     */
    public function addMeta($key, $value)
    {
        $existing = $this->meta()
            ->where('key', $key)
            ->where('value', Helpers::maybeEncode($value))
            ->first();
        if ($existing) return false;
        $meta = $this->meta()->create([
            'key'   => $key,
            'value' => $value,
        ]);
        return $meta->isSaved() ? $meta : $meta->getErrors();
    }

    /**
     * Appends a value to an existing meta entry
     * Resets all keys
     * @param $key
     * @param $value
     * @return mixed
     */
    public function appendMeta($key, $value)
    {
        $meta = $this->getMeta($key);
        if(!$meta) $meta = [];
        if(is_array($value))
        {
            $meta = array_merge($meta, $value);
        }
        else
        {
            $meta[] = $value;
        }
        return $this->updateMeta($key, array_values(array_unique($meta)));
    }

    /**
     * Deletes meta data
     * @param $key
     * @param bool $value
     * @return bool
     */
    public function deleteMeta($key, $value = false)
    {
        if ($value)
        {
            $meta = $this->getMeta($key, true);
            if ($meta == null) return false;
            $obj = $this->getEditableItem($meta, $value);
            return $obj !== false ? $obj->delete() : false;
        }
        else
        {
            return $this->meta()->where('key', $key)->delete();
        }
    }

    /**
     * Deletes all meta data
     * @return mixed
     */
    public function deleteAllMeta()
    {
        return $this->meta()->delete();
    }

    /**
     * Gets an item to edit
     * @param $meta
     * @param $value
     * @return bool
     */
    protected function getEditableItem($meta, $value)
    {
        if ($meta instanceof Collection)
        {
            if ($value === false) return false;
            $filtered = $meta->filter(function($m) use ($value)
            {
                return $m->value == $value;
            });
            $obj = $filtered->first();
            if ($obj == null) return false;
        }
        else
        {
            $obj = $meta;
        }
        return $obj->exists ? $obj : false;
    }

    /**
     * Attaches meta data
     * @return mixed
     */
    public function meta()
    {
        return $this->morphMany(Meta::class, 'metable');
    }
}