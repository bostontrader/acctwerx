#Test Plan

##Categories of Testing
There are several basic categories of testing that I'm interested in:

* BasicCRUD. Ordinary correct operation. If everything is ok, then it should work, right?

	I will call every URL I can enumerate. I want to verify basic functionality
	and screen content.  Also verify successful flash messages.

* Validation.  Make data-entry style errors to trigger validation messages.

* Request Errors. Make any other kind of error, reasonably within the bound of this
 application's testing.

This is a giant can of worms. Here are the considerations:

1. When an http request is received by nginx, nginx may use php-fpm and php
in order to produce a response. Said request can in principal be as bizarre, twisted, and malicious
as the evil genius of some demented hacker can possible conceive.  Which http verb? Which headers? What URL?
 Which query string and POST variables, etc. I assume that nginx, et.al. are tested
"well enough".  Since it's not feasible for me to improve upon their existing testing,
I'll instead simply rely upon it as "good enough."

2. When the request gets to CakePHP it will be processed by Cake's Router.  The Router
will parse the request and attempt to dispatch it to a controller method. My testing interest is anything
that gets through Cake's routing. If a request cannot get through Routing, I rely upon the testing
of the prior components in the stack.

3. Requests to this app are composed of:

    A. An http verb (ie. GET, POST...)
    B. A URL
        1. Known strings that identify the controllers and actions,
        2. Variable portions that identify specific records,
        3. Optionally query string parameters.
    C. Optionally POST/PUT variables.

I will rely upon the Cake Router to properly and securely deal with parsing this. I assume that "hacky"
things in the URL, such as Javascript exploits for example, will be weeded out before the Router.

4. Practically speaking, "get through routing" also means "get into a controller method." By the time control
 passes to a controller method I will assume that all hacky stuff is gone, but that we now need to test the
 basic elements of the request, as enumerated earlier.  The type of testing depends upon the http verb.
 For example, a GET request should not have POST parameters.  If it does, something hacky is going on.
 Even so, my methods will not look at the POST parameters, so no harm done. This is something that
 the testing for the other elements of the stack should ensure, and I'll not test this again myself.

5. The users will never create a URL so there's no need for any error-handling for them.
 The URLs created by the app are relied upon to be correct because other testing proves them to so.
 Again, no error-handling need apply.

The only reason we'd get bad urls is because somebody is fiddling
 with them.  Not only do they not deserve any error messages, we in fact want to keep them in the dark
 re: what works or doesn't work.




5. More specifically:

5.1 GET

5.1.1 Only whitelisted query string params are accepted, or maybe none at all.

5.1.1 Record id referential integrity.

A. Do the record ids, if any, in the URL, refer to existing records?

B. Are these records actually properly related?






  the
 record ids and their referential integrity, as well as query string parameters. These things are the

 , and optionally with POST/PUT parameters.

4. Routing can be configured to only allow specific verbs.  Controller methods can also use allowMethod
to specify the allowed http verbs.


Therefore I will not




GET add

 What errors are we looking for?  How can we detect them?

 Several of the errors return 400 bad request.  In the event that two of these
 errors might arise in a single method, then we cannot be certain that a
 particular error really is the cause of the 400 bad request. (maybe the other error
 occurred.)  But we _can_ send back a message and test for that if necessary.

 But that's not necessary. The user never creates URLs and the only way we'll see
 these errors is if somebody's messin' with the URL. If so, providing a user-friendly
 UI would be the least of our concerns.

 1. Each method has a small white-list of acceptable http verbs. Attempt access
 with another verb. Return 405 Method not allowed.

 2. Neither GET nor PUT should have any query params ($this->request->query).
 400. Bad request.

 3. POST should have a very specific list of POST variables.  Try to send an extra.
 400. Bad request.

 4. PUT should have a very specific list of POST variables.  Try to send an extra.
 400. Bad request.

 5. Trigger validation errors.

 These errors will be detected by Cake. Let's trust its functionality.

 1. GET, POST, or PUT should all have a book_id in their URLs.

 2. The book_id should refer to an existant book.  If the book_id is non-numeric,
 it refers to a non-existant book.

 3. Extra stuff at the end of the URL.

	Can I trigger validation messages?

* After all of the above, what does code coverage look like?

* How do I know that routing works correctly?

	Do all possible routes go somewhere?

	Can some urls get past routing and invoke SkyNet?

* Run db test scenarios.

* Does the html output adhere to standards?

##Fixture Data

Much testing involves the db. This is a notoriously difficult thing to test.  Here I describe my
approach.  But first a quick review:

We are using PHPUnit and IntegrationTestCase from CakePHP.  This requires a 'test' db configuration
in app.config.  When this runs, it will drop all the tables, if any, presently in the test db,
and create new ones, based on the fixtures specified in the test case.  This presents two obvious
issues: 1) Where to obtain/specify the schema for the required tables, and 2) where to actually
get the data.

If we specify the schema in the fixtures we would have to manually keep it in sync with everything
else and it definitely doesn't participate in the Cake migration process.  It is however easy 
to tell Cake testing to use the schema from another db, such as perhaps from myapp-dev,
by setting the $import member in a fixture class.  Hopefully this schema is more elegantly kept in synch
with everything else and would be a good source to copy. This will give us the schema, but not the data.

Getting the data is the hard part.  Here we have two basic choices: 1) Hard-wire example data in 
the fixture files, or 2) import from another db (as with the schema).  These choices are confounded
by the fact that realistic example data will include many records that are all intricately
wired together via their keys.  

We need to carefully ensure that there is sufficient variation
in order to exercise all the nooks and crannies of our code. For example, a list of classes for one
teacher should not display classes for another. To make sure this is not happening, we need to
have records refering to both teachers, and ensure that only the proper subset is used. In fact
I suggest that explicitly testing for this variation is desireable.  So for example, our list 
of records in one screen is known to be the desired subset, filtered from other records, instead
of all records, that just don't have sufficient variation.

We need some method of obtaining bad records.  We'll need something to trigger the various exceptions
and validations.

During testing we eventually need a fairly large db-style capacity for dealing with the
data.  For example, we need WHERE and ORDER clauses, we need to do joins, and we need to use
aggregate functions.  This is not real easy to do using hard-wired fixture data and looks quite
a lot like re-inventing wheels.

All that hand-wringing said, here's my approach:

1. Most of the fixture data lives in a db named myapp-fixture.  This is a showcase for perfect
data only.  Nothing in this db should violate any validation or constraint.



##Cheat Sheet
Execute phpunit.  Feed it whatever args you want...
cd $STACK_ROOT/html/acctwerx
$STACK_ROOT/php/bin/php vendor/bin/phpunit tests/TestCase/Controller

http://localhost/myapp?XDEBUG_SESSION_START=n
var_dump($n);
export XDEBUG_CONFIG="idekey=PHPSTORM"


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