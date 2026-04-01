<?php

namespace app\models\traits;


use Yii;
use yii\helpers\Inflector;
use yii\imagine\Image;

trait UploadFilesTrait {

    protected string $tempDirectory;
    protected string $directory;

    protected array $defaultConvertResolutions = ['width' => 1920, 'height' => 1080];
    protected array $convertResolutions = [
        ['width' => 1920, 'height' => 1272],
        ['width' => 960, 'height' => 640],
        ['width' => 480, 'height' => 320]
    ];

    /**
     * Создаём необходимые директории
     *
     * @param string $rootDirectory
     * @param string $path_to_file
     */
    protected function createDirectory(string $path_to_file, string $rootDirectory = 'uploads'): void {
        if (file_exists($rootDirectory)) {
            if (!file_exists($rootDirectory . '/temp_files')) {
                mkdir($rootDirectory . '/temp_files', 0755);
            }
            
            if (!file_exists($rootDirectory . '/' . $path_to_file)) {
                mkdir($rootDirectory . '/' . $path_to_file, 0755);
            }
        } else {
            mkdir($rootDirectory, 0755);
            mkdir($rootDirectory . '/temp_files', 0755);
            mkdir($rootDirectory . '/' . $path_to_file, 0755);
        }

        $this->tempDirectory = $rootDirectory . '/temp_files';
        $this->directory = $rootDirectory . '/' . $path_to_file;
    }

    /**
     * Функция загрузки изображения
     *
     * @param $model_name
     * @param $field
     * @param string $path_to_file
     * @param bool $createThumbnail
     * @param string|null $oldFile
     *
     * @return bool|string
     */
    protected function uploadImage($model_name, $field, string $path_to_file = 'undefined', bool $createThumbnail = true,
                                   string $oldFile = null): bool|string {
        $this->createDirectory($path_to_file);

        if ($model_name->validate()) {
            $name_image = preg_replace('/\s+/', '_',
                    Inflector::transliterate($model_name->$field->baseName)) . '.' . $model_name->$field->extension;

            if (in_array($model_name->$field->type, Yii::$app->params['mimeTypesImage'])) {
                $model_name->$field->saveAs($this->tempDirectory . '/' . $name_image);

                $path = $this->treatmentImage($name_image, $createThumbnail);
            } else {
                $path = 'uploads/' . $path_to_file . '/' . $name_image;

                $model_name->$field->saveAs($path);
            }

            if ($oldFile != null) {
                @unlink($oldFile);
            }

            return $path;
        } else {
            return false;
        }
    }

    /**
     * Функция загрузки нескольких изображений
     *
     * @param $model_name
     * @param $field
     * @param string $path_to_file
     *
     * @return array|bool
     * @throws \ImagickException
     */
    protected function uploadGallery($model_name, $field, string $path_to_file = 'undefined'): bool|array {
        $this->createDirectory($path_to_file);

        $arrFile = [];
        if ($model_name->validate()) {
            foreach ($model_name->$field as $key => $file) {
                $name_image = preg_replace('/\s+/', '_', Inflector::transliterate($file->baseName))
                    . '.' . $file->extension;

                if ($file->type == 'image/jpeg' || $file->type == 'image/pjpeg' || $file->type == 'image/png') {
                    $file->saveAs($this->tempDirectory . '/' . $name_image);

                    $path = $this->treatmentImage($name_image);

                    $arrFile[$key]['type'] = 0;
                } else {
                    $path = 'uploads/' . $path_to_file . '/' . preg_replace('/\s+/', '_',
                            Inflector::transliterate($file->baseName)) . '.' . $file->extension;
                    $file->saveAs($path);

                    $arrFile[$key]['type'] = 1;
                }

                $arrFile[$key]['name'] = $name_image;
                $arrFile[$key]['path'] = $path;
            }

            return $arrFile;
        } else {
            return false;
        }
    }
	
	/**
     * Функция удаления файлов
     *
     * @param $model_name
     * @param $field
     *
     * @param string $type_model
     *
     * @return bool|string
     */
    protected function deleteImages($model_name, $field, string $type_model = 'array'): bool|string {
        if ($type_model === 'array') {
            if (!empty($model_name->$field)) {
                foreach ($model_name->$field as $file) {
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
            }
        } else {
            if (file_exists($model_name->$field)) {
                if (!empty($this->convertResolutions)) {
                    $arrayNameImage = explode('/', $model_name->$field);
                    $count = mb_strlen($arrayNameImage[0]);
                    $count += mb_strlen($arrayNameImage[1]);
                    $name_image = mb_substr($model_name->$field, $count + 2);

                    foreach ($this->convertResolutions as $resolution) {
                        $convertName = $arrayNameImage[0] . '/' . $arrayNameImage[1] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $name_image;

                        if (file_exists($convertName)) {
                            @unlink($convertName);
                        }
                    }
                }

                @unlink($model_name->$field);
            }
        }

        return true;
    }

    /**
     * Обрабатываем картинки
     *
     * @param $name_image
     * @param bool $createThumbnail
     *
     * @return string
     */
    protected function treatmentImage($name_image, bool $createThumbnail = false): string {
        $tempNameFile = $this->tempDirectory . '/' . $name_image;
        $randTempNameFile = $this->tempDirectory . '/' . time() . '_' . $name_image;
        $path = $this->directory . '/' . $name_image;

        Image::autorotate(Yii::getAlias('@webroot/' . $tempNameFile))
            ->save(Yii::getAlias('@webroot/' . $randTempNameFile), ['quality' => 90]);

        shell_exec('convert ' . Yii::getAlias('@webroot/' . $randTempNameFile)
            . ' ' . Yii::getAlias('@webroot/' . $this->tempDirectory . '/')
            . substr($name_image, 0, -4) . '_profile.icm');
        shell_exec('convert ' . Yii::getAlias('@webroot/' . $randTempNameFile)
            . ' -strip -profile ' . Yii::getAlias('@webroot/' . $this->tempDirectory . '/')
            . substr($name_image, 0, -4) . '_profile.icm ' . Yii::getAlias('@webroot/' . $path));

        if ($createThumbnail && !empty($this->convertResolutions)) {
            foreach ($this->convertResolutions as $resolution) {
                $convertName = $this->directory . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $name_image;

                Image::resize(Yii::getAlias('@webroot/' . $path), $resolution['width'], $resolution['height'])
                    ->save(Yii::getAlias('@webroot/' . $convertName));
            }
        }

        Image::resize(Yii::getAlias('@webroot/' . $path), $this->defaultConvertResolutions['width'], $this->defaultConvertResolutions['height'])
            ->save(Yii::getAlias('@webroot/' . $path));

        @unlink(Yii::getAlias('@webroot/' . $this->tempDirectory . '/') . substr($name_image, 0, -4) . '_profile.icm');
        @unlink($tempNameFile);
        @unlink($randTempNameFile);

        return $path;
    }
}
