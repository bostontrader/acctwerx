<?php

namespace App\Test\TestCase\Controller;

//use App\Test\Fixture\FixtureConstants;
//use App\Test\Fixture\UsersFixture;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\TestSuite\IntegrationTestCase;

/**
 *
 * In these tests I generally want to test that:
 *
 * 1. A controller method exists...
 *
 * 2. Said method returns the correct response code.
 *
 * 3. Said method does or does not redirect.  If it redirects, then where to?
 *
 * 4. A bare minimum of html structure required to reasonably verify correct operation
 *    and to facilitate TDD.  For example, the add method should return a form with certain fields,
 *    and particular <A> tag should exist.
 *
 * 5. Verify that the db has changed as expected, if applicable.
 *
 * 6. Whether or not Auth prevents/allows access to a method.
 *
 * I do not want to test (here):
 *
 * 1. How the method responds to badly formed requests, such as trying to submit a DELETE to the add method.
 *
 * 2. Any html structure, formatting, css, scripts, tags, krakens, or whatever, beyond the bare minimum
 *    listed above.
 *
 * 3. Whether or not following an <A> tag actually works as expected.
 *
 * These items should be tested elsewhere.
 *
 * Although tempting to test for viewVars, resist the urge.  If they are not set correctly then
 * there will be actual consequences that the testing will catch.  At best looking for viewVars
 * is a debugging aid.  At worst, we'll eat a lot of time picking them apart.  Just say No.
 *
 * Input control validation:
 *
 * There are several methods of verifying that a particular input or select control
 * is correct.  For example, we may want to search for a control based on its id or name, may want
 * to ensure that it's blank or has some particular value.  Perhaps the control should be of a
 * certain type such as 'text' or 'hidden', or perhaps a select control should be set to a particular
 * selection or 'no selection'.  If we're not careful, these variations can give rise to a blizzard
 * of similar methods, that operate in a similar, but inconsitent manner.  But because we _are_ careful,
 * we've abstracted all this out in a reasonably sensible manner.  Here's how it works...
 *
 * Each attribute about a control of interest can be described using a simple css selector string.
 *
 *
 * A. The control exists and can be found using a css finder string., is of some given type, and has a specified value.
 * B. The input is a select, with a given css finder string, and has a specified value.
 */

class DMIntegrationTestCase extends IntegrationTestCase {

    /* var \App\Test\Fixture\UsersFixture */
    //protected $usersFixture;

    /* @var \simple_html_dom_node */
    //private $input;

    /**
     * A. The input has a given id, is of some given type, and has a specified value.
     * @param \simple_html_dom_node $html_node the form that contains the select
     * @param String $css_finder A css finder string to find the input of interest. Note: This only
     * does very simple css.
     * @param mixed $expected_value What is the expected value of the input, or false if expected to be empty.
     * @param String $type What is the type attribute of the input?
     * @return boolean true if a matching input is found, else assertion failure.
     */
    //protected function inputCheckerA($html_node,$css_finder,$expected_value=false,$type='text'){
        /* @var \simple_html_dom_node $input */
        //$n1=$html_node->find('input[type=text]');
        //$n2=$html_node->find($css_finder);
        //$input = $html_node->find($css_finder,0);
        //$this->assertEquals($input->type, $type);
        //$this->assertEquals($expected_value,$input->value);
        //return true;
    //}

    /**
     * Many forms have a hidden input for various reasons, such as for tunneling various http verbs using POST,
     * or for implementing multi-select lists.
     * Look for the first one of these present.  If found, return true, return false.
     * @param \simple_html_dom_node $form the form that contains the select
     * @param String $name the name attribute of the input
     * @param String $value the value of the input
     * @return boolean | \simple_html_dom_node
     */
    //protected function lookForHiddenInput($form, $name='_method', $value='POST') {

        //$n1=$form->find('input[type=hidden]');
        //$n2=$form->find('input#_method');
        //$n2=$form->find('input#book_id');
        //foreach($form->find('input[type=hidden]') as $input) {
            //if($input->value == $value && $input->name == $name)
                //return $input;
        //}
        //return false;
    //}

    /**
     * Look for a particular select input merely to ensure that it
     * exists.  Optionally an ensure that the control has the correct quantity of choices.
     *
     * Note: May also want to ensure that the selection has nothing selected.
     * May also want to verify the
     *
     * @param \simple_html_dom_node $form the form that contains the select
     * @param string $selectID the html id of the select of interest
     * @param string $vvName the name of the view var that contains the info to populate the select
     * control. If null, do no further testing.
     * @param boolean $noneSelected.  If true, the count of choices in the select, should be
     * one more than that of the view variable, because "none selected" is included as a choice.
     * @return boolean true if a matching select is found, else assertion failures.
     */
    //protected function selectCheckerA($form, $selectID, $vvName=null,$noneSelected=true) {
        //$option = $form->find('select#'.$selectID.' option[selected]', 0);
        //$this->assertNull($option);
        //$option_cnt = count($form->find('select#'.$selectID. ' option'));
        //$record_cnt = $this->viewVariable($vvName)->count();
        //$this->assertEquals($record_cnt + 1, $option_cnt);
        //return true;
    //}


    /**
     * Login and submit a POST request to a $url that is expected to delete a given record,
     * and then verify its removal.
     *
     * @param int $user_id The user to login as.
     * @param String $url The url to send the request to.  Be sure to include a trailing /.
     * @param int $delete_id The id of the record to delete.
     * @param String $redirect_url The url to redirect to, after the deletion.
     * @param \Cake\ORM\Table $table The table to delete from.
     */
    //protected function deletePOST($user_id, $url, $delete_id, $redirect_url, $table) {

        //$this->fakeLogin($user_id);
        //$this->post($url . $delete_id);
        //$this->assertResponseCode(302);
        //$this->assertRedirect($redirect_url);

        // Now verify that the record no longer exists
        //$query=new Query(ConnectionManager::get('test'),$table);
        //$query->find('all')->where(['id' => $delete_id]);
        //$this->assertEquals(0, $query->count());
    //}




    /**
     * CakePHP will automatically generate a group of select inputs that
     * can be used to enter the components of a datetime column.
     *
     * Ensure that there are suitable select fields for a datetime. Don't
     * worry about checking their default values or available choices because that's
     * Cake's responsibility and presumably already tested.
     *
     * @param \simple_html_dom_node $html_node the form that contains the select fields.
     * @param String $css_finder_root A root css finder string to find the select of interest. This method
     * will append various suffixes such as '[year]' or '[month]' when looking for the individual select
     * fields of the group. Note: This only does very simple css.
     * @return int the number of select fields found.  Should be 5.
     */
    //protected function inputCheckerDatetime($form,$css_finder_root) {

        // 1. Ensure that there's a select field for 'year'.  Assume, but don't check,
        // that it's set to a default of the present year.  Don't worry about the quantity
        // of available choices.
        //$selectInputsFound=0;
        //if($this->selectCheckerA($form, $css_finder_root.'[year]')) $selectInputsFound++;

        //return $selectInputsFound;
    //}



    /**
     * Login and submit a POST request to a $url that is expected to add a given record.
     * Retrieve the record with the highest id, which we hope is the new record we just
     * added, and return that to the caller.
     *
     * @param int $user_id The user to login as.
     * @param String $url The url to send the request to.
     * @param array $newRecord
     * @param String $redirect_url The url to redirect to, after the deletion.
     * @param \Cake\ORM\Table $table The table to receive the new record.
     * @return \Cake\ORM\Entity The newly added record, as read from the db.
     */
    protected function genericPOSTAddProlog($user_id, $url, $newRecord, $redirect_url, $table) {

        //$this->fakeLogin($user_id);
        $this->post($url, $newRecord);
        $this->assertResponseCode(302);
        $this->assertRedirect( $redirect_url );

        // Now retrieve the newly written record.
        //$query=new Query(ConnectionManager::get('test'),$table);
        //$fromDbRecord=$query->find('all')->order(['id' => 'DESC'])->first();
        $fromDbRecord=$table->find('all')->order(['id' => 'DESC'])->first();
        return $fromDbRecord;
    }

    /**
     * Login and submit a PUT request to a $url that is expected to update
     * a given record. Then redirect to a given $redirect_url, read the updated record
     * from the $table and return it to the caller.
     *
     * @param int $user_id The user to login as.
     * @param String $url The url to send the request to.
     * @param int $id The primary key of the $post_data record.
     * @param array $post_data
     * @param String $redirect_url The url to redirect to, after the update.
     * @param \Cake\ORM\Table $table The table to receive the new record.
     * @return \Cake\ORM\Entity The newly added record, as read from the db.
     */
    //protected function genericEditPutProlog($user_id, $url, $id, $post_data, $redirect_url, $table) {
        //$connection=ConnectionManager::get('test');
        //$query=new Query($connection,$table);

        //$this->fakeLogin($user_id);
        //$this->put($url.'/'.$id, $post_data);
        //$this->assertResponseCode(302);
        //$this->assertRedirect( $redirect_url );

        // Now retrieve that 1 record and send it back.
        //$query=new Query($connection,$table);
        //return $table->get($id);
    //}

    /**
     * Many tests need to login, issue a GET request, and receive and parse a response.
     *
     * @param int $user_id Who shall we login as? If null, don't login.
     * @param String $url
     * @return \simple_html_dom_node $html parsed dom that contains the response.
     */
    //protected function loginRequestResponse($user_id, $url) {

        // 1. Simulate login, submit request, examine response.
        //if(!is_null($user_id)) $this->fakeLogin($user_id);
        //$this->get($url);
        //$this->assertResponseCode(200);
        //$this->assertNoRedirect();

        // 2. Parse the html from the response
        //return str_get_html($this->_response->body());
    //}

    /**
     * During many tests we determine the number of inputs, selects, and atags that we
     * should have, compare that do what we actually test, a determine a final quantity
     * of unaccounted for elements. These three quantities should all be zero.
     * @param int $unknownInputCnt The quantity of unaccounted-for input tags.
     * @param int $unknownSelectCnt The quantity of unaccounted-for select tags
     * @param \simple_html_dom_node $html parsed dom that contains the response.
     * @param String $css_finder A css finder string to find a region of the html to search for
     * Atags.
     */
    //protected function expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, $css_finder) {
        //$this->assertEquals(0, $unknownInputCnt);
        //$this->assertEquals(0, $unknownSelectCnt);

        // Examine the <A> tags on this page.  There should be zero links.
        /* @var \simple_html_dom_node $content */
        //$content = $html->find($css_finder,0);
        //$this->assertNotNull($content);
        //$links = $content->find('a');
        //$this->assertEquals(0,count($links));
    //}




    /**
     * B.
     * @param \simple_html_dom_node $html_node the form that contains the select
     * @param String $css_finder A css finder string to find the input of interest. Note: This only
     * does very simple css.
     * @param String $expected_id The expected value of the select.
     * @param String $expected_display. The expected value to be displayed.
     * @return boolean Return true if a matching input is found, else assertion errors.
     */
    //protected function inputCheckerB($html_node,$css_finder,$expected_id,$expected_display){
    //$option = $html_node->find($css_finder,0);
    //$this->assertEquals($expected_id, $option->value);
    //$this->assertEquals($expected_display, $option->plaintext);
    //return true;
    //}


    /**
     * Look for a particular select input and ensure that:
     * The selection is what is expected and that the selection control
     * has the correct quantity of choices.  If the control passes, return true, else fail.
     *
     * @param \simple_html_dom_node $form the form that contains the select
     * @param string $selectID the html id of the select of interest
     * @param string $vvName the name of the view var that contains the info to populate the select
     * @return boolean true if successful else assertion failures.
     */
    //protected function selectCheckerB() {

    //}

    // Hack the session to make it look as if we're properly logged in.
    //protected function fakeLogin($userId) {

        //if($userId==null) return; // anonymous user, not logged in

        // Set session data
        //$username = $this->usersFixture->get($userId)['username'];
        //$this->session(
            //[
                //'Auth' => [
                    //'User' => [
                        //'id' => $userId,
                        //'username' => $username
                    //]
                //]
            //]
        //);
    //}



    /**
     * @deprecated use selectCheckerA
     * Look for a particular select input and ensure that:
     * The selection is what is expected and that the selection control
     * has the correct quantity of choices.  If the control passes, return true, else fail.
     *
     * @param \simple_html_dom_node $form the form that contains the select
     * @param string $selectID the html id of the select of interest
     * @param string $vvName the name of the view var that contains the into to populate the select
     * @return boolean
     */
    //protected function lookForSelect($form, $selectID, $vvName) {
        //$option = $form->find('select#'.$selectID.' option[selected]', 0);
        //$this->assertNull($option);
        //$option_cnt = count($form->find('select#'.$selectID. ' option'));
        //$record_cnt = $this->viewVariable($vvName)->count();
        //$this->assertEquals($record_cnt + 1, $option_cnt);
        //return true;
    //}

    //public function setUp() {
        //parent::setUp();
        //$this->usersFixture = new UsersFixture();
    //}

    //private $requests2Try=[
        //['method'=>'add','verb'=>'get'],
        //['method'=>'add','verb'=>'post'],
        //['method'=>'delete','verb'=>'post'],
        //['method'=>'edit','verb'=>'get'],
        //['method'=>'edit','verb'=>'put'],
        //['method'=>'index','verb'=>'get'],
        //['method'=>'view','verb'=>'get']
    //];

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    // This is a stop-gap measure until more thorough testing is implemented in the various controllers.
    //protected function tstUnauthenticatedActionsAndUsers($controller) {
        //foreach($this->requests2Try as $request2Try) {
            //$this->tstNotAllowedRequest($request2Try['verb'], '/'.$controller.'/'.$request2Try['method'], '/users/login');
        //}
    //}

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    // This is a stop-gap measure until more thorough testing is implemented in the various controllers.
    //protected function tstUnauthorizedActionsAndUsers($controller) {

        //$userIds = [
            //FixtureConstants::userArnoldAdvisorId,
            //FixtureConstants::userSallyStudentId,
            //FixtureConstants::userTommyTeacherId,
        //];

        //foreach($this->requests2Try as $request2Try) {
            //foreach($userIds as $userId) {
                //$this->fakeLogin($userId);
                //$this->tstNotAllowedRequest($request2Try['verb'], '/'.$controller.'/'.$request2Try['method'], '/');
            //}
        //}
    //}

    // There are many tests that try to submit an html request to a controller method,
    // where the user is not allowed to access said page. Either because he's unauthenticated
    // or not authorized. This method will submit the
    // request and assert redirection to the login page.
    //protected function tstNotAllowedRequest($verb, $url, $redirection_target) {

        //if($verb=='get')
            //$this->get($url);
        //else if($verb=='post')
            //$this->post($url);
        //else if($verb=='put')
            //$this->put($url);

        //$this->assertResponseSuccess(); // 2xx, 3xx
        //$this->assertRedirect( $redirection_target );
    //}

}
