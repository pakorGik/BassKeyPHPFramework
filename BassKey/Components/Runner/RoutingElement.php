<?php

namespace BassKey\Components\Runner;


class RoutingElement
{
    /**
     * @var string $name
     */
    private $name = null;

    /**
     * @var array $addressList
     */
    private $addressList = null;

    /**
     * @var string $controller
     */
    private $controller = null;

    /**
     * @var string $view
     */
    private $view = null;

    /**
     * @var string $fileType
     */
    private $fileType = "";

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getAddressList(): array
    {
        return $this->addressList;
    }

    /**
     * @param array $addressList
     */
    public function setAddressList(array $addressList): void
    {
        $this->addressList = $addressList;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }
}
