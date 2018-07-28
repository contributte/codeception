<?php declare(strict_types = 1);

namespace Contributte\Codeception\Qa\PhpStan;

use Contributte\Codeception\Module\NetteDIModule;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class NetteDIModuleType implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return NetteDIModule::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'grabService';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		if ($methodCall->args === []) {
			return $methodReflection->getReturnType();
		}

		$arg = $methodCall->args[0]->value;

		if (!$arg instanceof ClassConstFetch) {
			return $methodReflection->getReturnType();
		}

		$class = $arg->class;

		if (!$class instanceof Name) {
			return $methodReflection->getReturnType();
		}

		$class = (string) $class;

		if ($class === 'static') {
			return $methodReflection->getReturnType();
		}

		if ($class === 'self') {
			$class = $scope->getClassReflection()->getName();
		}

		return new ObjectType($class);
	}

}
