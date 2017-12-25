<?php

namespace Drupal\iquiz;


use Drupal\Component\Plugin\PluginInspectionInterface;

interface QuizTypePluginInterface extends  PluginInspectionInterface {

    public function sortQuestionInstances($quiz_id = NULL);

    public function isShuffled();

} 