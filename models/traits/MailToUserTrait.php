<?php

namespace app\models\traits;


use Yii;
use yii\mail\MessageInterface;

trait MailToUserTrait {

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @param string $view
     *
     * @param string $subject
     * @param array $params
     * @param string|null $key_file
     *
     * @return MessageInterface
     */
    public function sendMailToUser(string $email, string $view, string $subject, array $params = [],
                                   string $key_file = null): MessageInterface {
		if (!empty($params)) {
			foreach ($params as $key_p => $value_p) {
				if ($key_p != $key_file) {
					Yii::$app->mailer->getView()->params[$key_p] = $value_p;
				}
			}
		}

        $result = Yii::$app->mailer->compose([
            'html' => 'views/' . $view . '-html',
            'text' => 'views/' . $view . '-text',
        ], $params);
		
		if ($key_file !== null && !empty($params[$key_file])) {
            foreach ($params[$key_file] as $file) {
                $content_file = file_get_contents($file->tempName);
                $result->attachContent($content_file, [
                    'fileName' => $file->baseName . '.' . $file->extension,
                    'contentType' => $file->type]);
            }
        }

        $result->setTo($email);
        $result->setSubject($subject);
        $result->send();

		if (!empty($params)) {
			foreach ($params as $key_p => $value_p) {
				if ($key_p != $key_file) {
					Yii::$app->mailer->getView()->params[$key_p] = null;
				}
			}
		}
		
        return $result;
    }

}
