<?php

namespace App\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadHelper
{
    private readonly string $publicPath;

    public function __construct(ContainerBagInterface $containerBag)
    {
        $this->publicPath = $containerBag->get('kernel.project_dir').'/public';
    }

    public function uploads(array $uploads): ?array {
        $self = $this;
        return array_map(
            function ($item) use($self) {
                return $self->upload($item);
            },
            $uploads
        );
    }

    public function unlink(?string $file = ''): void {
        if(!empty($file)) {
            @unlink("{$this->publicPath}{$file}");
        }

    }

    public function upload(UploadedFile $file, $path = '/documents'): string
    {
        $now = new \DateTime('now');

        $filename = $this->generateUniqueName($file);

        if ('image/svg' === $file->getMimeType()) {
            $filename .= 'svg';
        }

        $path = "{$path}/{$now->format('Y')}/{$now->format('m')}";

        $file->move($this->publicPath."$path", $filename);

        return "$path/$filename";
    }

    public function generateUniqueName(UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        if (!$extension) {
            $temp= explode('.',$file->getClientOriginalName());
            $extension = end($temp);
        }
        return md5(uniqid()).'.'.$extension;
    }
}