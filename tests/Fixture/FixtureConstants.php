<?php

namespace App\Test\Fixture;

/**
 * Class FixtureConstants
 * @package App\Test\Fixture

 * The test fixtures are extracted from a fixture db. The fixture db is maintained using the
 * actual application. The actual data required to support the byzantine collection of test cases
 * is just too complicated to assemble by hand. Hence these contortions.
 *
 * After using the app to tweak the fixture db, we will frequently want to reference specific records
 * in our testing. In this class, we tediously maintain constants pointing to these specific records,
 * using the value of the primary key.
 * Making these constants point to the correct records is of critical importance.
 */

class FixtureConstants {

    const accountTypical = 1;
    const bookTypical = 1;
    const categoryAsset = 1;
    const categoryExpense = 5;
    const currencyTypical = 1;
    const distributionTypical = 1;
    const transactionTypical = 1;

}