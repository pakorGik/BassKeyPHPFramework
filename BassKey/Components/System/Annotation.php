<?php

namespace BassKey\Components\System;

use Doctrine\Common\Annotations\AnnotationReader;

class Annotation
{
    /**
     * @var array $annotationClassList
     */
    private $annotationClassList = array();

    /**
     * @return array
     */
    public function getAnnotationClassList(): array
    {
        return $this->annotationClassList;
    }

    /**
     * @param array $annotationClassList
     */
    public function setAnnotationClassList(array $annotationClassList): void
    {
        $this->annotationClassList = $annotationClassList;
    }

    private function readAnnotation($className): bool
    {

        $classAnnotations = null;

        try {

            $reflectionClass = new \ReflectionClass($className);
            $classAnnotations = (new AnnotationReader())->getClassAnnotations($reflectionClass);

        } catch (\Exception $exception) {

            return false;

        }

        foreach ($classAnnotations AS $annotation) {

            if (!$annotation instanceof AnnotationRuler)
            {
                continue;
            }

            $annotation->runAnnotation;
        }

        return true;

    }

    public function readAnnotations(): void
    {

        foreach ($this->annotationClassList AS $annotationClass) {

            $this->readAnnotation($annotationClass);

        }

    }

}
