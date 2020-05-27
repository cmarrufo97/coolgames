<?php

use app\models\Usuarios;

class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Usuario o e-mail', 'label');

    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $I->amLoggedInAs(Usuarios::findOne(1));
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\Usuarios::findPorLogin('admin'));
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Login no puede estar vacío.');
        $I->see('Password no puede estar vacío.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[login]' => 'admin',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Usuario o contraseña incorrectos');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[login]' => 'admin',
            'LoginForm[password]' => 'admin',
        ]);
        $I->see('Logout (admin)');
        $I->dontSeeElement('form#login-form');              
    }
}
