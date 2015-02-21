<?php

namespace SourceBundle\Interfaces\Handler;

interface Collect {

    /**
     * @param array $paging
     * @return $this
     */
    public function setPaging(array $paging);

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters);

    /**
     * @param array $settings
     * @return $this
     */
    public function setSettings(array $settings);
}
