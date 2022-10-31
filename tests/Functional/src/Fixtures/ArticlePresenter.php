<?php declare(strict_types = 1);

namespace Tests\Functional\Fixtures;

use Nette\Application\UI\Presenter;

class ArticlePresenter extends Presenter
{

	public function actionRedirect(): void
	{
		$this->redirectPermanent('page');
	}

	/**
	 * @return string[]
	 */
	public function formatTemplateFiles(): array
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$template = sprintf('%s/../../templates/%s.%s.latte', __DIR__, $presenter, $this->view);
		return [$template];
	}

}
