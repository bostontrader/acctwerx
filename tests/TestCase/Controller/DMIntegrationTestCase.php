<?php

namespace App\Test\TestCase\Controller;

//use App\Test\Fixture\FixtureConstants;
//use App\Test\Fixture\UsersFixture;
//use Cake\Datasource\ConnectionManager;
//use Cake\ORM\Query;
use Cake\TestSuite\IntegrationTestCase;

class DMIntegrationTestCase extends IntegrationTestCase {

    /* var \App\Test\Fixture\UsersFixture */
    //protected $usersFixture;

    public function setUp() {
        // The tests make heavy use of DOMDocument and DOMXpath and these
        // need this....
        // Use internal libxml errors -- turn on in production, off for debugging
        // DomDocument can deal with mal-formed html, but will generate lots of spurious errors.
        // Use this to make the errors go away.
        libxml_use_internal_errors(true);        
    }

    /**
     * Search for an element using a given xpath_expression.  Verify
     * that there is exactly one of them, and then return it.
     * 
     * @param \DomXPath $xpath
     * @param string $xpath_expression
     * @param \DOMNode $context_node
     * @return \DOMNode 
     */
    public function getTheOnlyOne($xpath, $xpath_expression, $context_node=null) {

        is_null($context_node) ?
            $nodes=$xpath->query($xpath_expression) :
            $nodes=$xpath->query($xpath_expression,$context_node);

        $this->assertEquals($nodes->length, 1);
        return $nodes->item(0);
    }

    /**
     * Ensure that there's a select control with the given select_id,
     * that it has the correct quantity of available choices, and that the
     * correct choice is selected and displayed.
     *
     * This is very similar to selectCheckerB, but we'd need extra variables and
     * conditionals to make everything work.  Simpler to just have two methods.
     *
     * @param \DomXPath $xpath
     * @param string $select_id
     * @param string $vv_name The name of the view variable that contains the select choices.
     * @param array $expected_choice.  If null, then no selection.  Else set the value and text
     * keys.
     * @param \DOMNode $context_node
     * @return boolean true if the select is found and passes the tests, else some assertion failure.
     */
    public function selectCheckerA($xpath,$select_id,$vv_name,$expected_choice=null,$context_node=null) {

        // 1. Get the one and only one select control.
        $select_node=$this->getTheOnlyOne($xpath,"//select[@id='$select_id']",$context_node);

        // 2. Make sure it has the correct number of choices, including an
        // extra for the none-selected choice.
        $record_cnt = $this->viewVariable($vv_name)->count();
        $this->assertEquals($xpath->query(".//option",$select_node)->length,$record_cnt+1);

        // 3. Verify the correct choice.
        if(is_null($expected_choice)) {
            // Make sure that none of the choices are selected.
            $this->assertTrue($xpath->query("//option[selected]",$select_node)->length==0);
        } else {
            // This specific choice should be selected.
            $value=$expected_choice['value']; $text=$expected_choice['text'];
            $nodes=$xpath->query(
                "//option[@selected='selected' and @value='$value' and text()='$text']",$select_node);
            $this->assertTrue($nodes->length==1);
        }
        return true;
    }

    /**
     * Ensure that there's a select control with the given $xpression,
     * that it has the correct quantity of available choices, and that the
     * correct choice is selected and displayed.
     *
     * This is very similar to selectCheckerA, but we'd need extra variables and
     * conditionals to make everything work.  Simpler to just have two methods.
     *
     * @param \DomXPath $xpath
     * @param string $xpression An xpath expression to identify the select.
     * @param array $expected_choice.  If null, then no selection.  Else set the value and text
     * keys.
     * @param \DOMNode $context_node
     * @return boolean true if the select is found and passes the tests, else some assertion failure.
     */
    public function selectCheckerB($xpath,$xpression,$choice_cnt,$expected_choice=null,$context_node=null) {

        // 1. Get the one and only one select control.
        //$select_node=$this->getTheOnlyOne($xpath,"//select[@id='$select_id']",$context_node);
        $select_node=$this->getTheOnlyOne($xpath,$xpression,$context_node);

        // 2. Make sure it has the correct number of choices.  But do _not_ include
        // an extra for the none-selected choice.
        //$record_cnt = $this->viewVariable($vv_name)->count();
        $this->assertEquals($xpath->query(".//option",$select_node)->length,$choice_cnt);

        // 3. Verify the correct choice.
        if(is_null($expected_choice)) {
            // Make sure that none of the choices are selected.
            // Ignore the selected choice.
            //$this->assertTrue($xpath->query("//option[selected]",$select_node)->length==0);
        } else {
            // This specific choice should be selected.
            $value=$expected_choice['value']; $text=$expected_choice['text'];
            $nodes=$xpath->query(
                "//option[@selected='selected' and @value='$value' and text()='$text']",$select_node);
            $this->assertTrue($nodes->length==1);
        }
        return true;
    }

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
     * @param \DomXPath $xpath
     * @param string $select_id
     * @param string $vv_name The name of the view variable that contains the select choices.
     * @param array $expected_choice.  If null, then no selection.  Else set the value and text
     * keys.
     * @param \DOMNode $context_node
     * @return boolean true if the select is found and passes the tests, else some assertion failure.
     * @return int the number of select fields found.  Should be 5.
     */
    protected function inputCheckerDatetime($xpath,$select_id,$vv_name,$expected_choice=null,$context_node=null) {

        // 1. Ensure that there's a select field for 'year'.  Assume, but don't check,
        // that it's set to a default of the present year.  Don't worry about the quantity
        // of available choices.
        //$selectInputsFound=0;
        //if($this->selectCheckerA($form, $css_finder_root.'[year]')) $selectInputsFound++;

        //return $selectInputsFound;
    }



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
     * @param boolean $redirect2_new_id By default, redirection should go to $redirect_url. However,
     * if $redirect2_new_id=true, then redirect to $redirect_url/$redirect2_new_id.
     *
     * @return \Cake\ORM\Entity The newly added record, as read from the db.
     */
    protected function genericPOSTAddProlog($user_id, $url, $newRecord, $redirect_url, $table, $redirect2_new_id=false) {

        //$this->fakeLogin($user_id);
        $this->post($url, $newRecord);

        // Now retrieve the newly written record.
        $fromDbRecord=$table->find('all')->order(['id' => 'DESC'])->first();

        $this->assertResponseCode(302);
        
        if($redirect2_new_id)
            $this->assertRedirect( "$redirect_url/$fromDbRecord->id" );
        else
           $this->assertRedirect( $redirect_url );

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
