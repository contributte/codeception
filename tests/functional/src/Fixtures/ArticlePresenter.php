<?php

namespace Tests\Functional\Fixtures;

use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ArticlePresenter extends Presenter
{
    public function actionRedirect()
    {
        $this->redirectPermanent('page');
    }

    public function formatTemplateFiles(): array
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':'.$name, ':'));

        return [__DIR__."/../../templates/$presenter.$this->view.latte"];
    }
}
