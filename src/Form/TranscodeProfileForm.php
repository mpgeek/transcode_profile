<?php

namespace Drupal\transcode_profile\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TranscodeProfileForm.
 */
class TranscodeProfileForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $transcode_profile = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $transcode_profile->label(),
      '#description' => $this->t("Label for the Transcode profile."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $transcode_profile->id(),
      '#machine_name' => [
        'exists' => '\Drupal\transcode_profile\Entity\TranscodeProfile::load',
      ],
      '#disabled' => !$transcode_profile->isNew(),
    ];

    // Custom property.
    $form['codec'] = [
      '#type' => 'textfield',
      '#title' => 'Codec',
      '#maxlength' => 255,
      '#description' => $this->t('The video codec to use for this profile.'),
      '#default_value' => $transcode_profile->getCodec(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $transcode_profile = $this->entity;

    // Set a custom property on the entity before save.
    $transcode_profile->setCodec($form_state->getValue('codec'));

    $status = $transcode_profile->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Transcode profile.', [
          '%label' => $transcode_profile->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Transcode profile.', [
          '%label' => $transcode_profile->label(),
        ]));
    }

    // Redirect to the collection route.
    $form_state->setRedirectUrl($transcode_profile->toUrl('collection'));
  }

}
