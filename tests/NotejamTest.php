<?php
class NotejamTest extends \Asgard\Http\Test {
	protected $user;

	protected function login() {
		$browser = $this->createBrowser();
		$browser->getSession()->set('user', $this->user->id);
		return $browser;
	}

	public function setUp() {
		$this->getContainer()['schema']->emptyAll();
	}

	#SIGN UP
	public function testSignupSuccess() {
		$before = \Notejam\Entity\User::count();

		$this->createBrowser()->post('signup', [
			'user' => [
				'email' => 'test@test.com',
				'password' => 'test',
				'confirm' => 'test'
			]
		]);

		$after = \Notejam\Entity\User::count();
		$this->assertEquals($before+1, $after, 'user can successfully sign up');
	}

	public function testSignupRequired() {
		$before = \Notejam\Entity\User::count();

		$this->createBrowser()->post('signup', [
			'user' => [
				'email' => '',
				'password' => 'test',
				'confirm' => 'test'
			]
		]);

		$after = \Notejam\Entity\User::count();
		$this->assertEquals($before, $after, 'user can\'t sign up if required fields are missing');
	}
	
	public function testSignupInvalid() {
		$before = \Notejam\Entity\User::count();

		$this->createBrowser()->post('signup', [
			'user' => [
				'email' => 'test',
				'password' => 'test',
				'confirm' => 'test'
			]
		]);

		$after = \Notejam\Entity\User::count();
		$this->assertEquals($before, $after, 'user can\'t sign up if email is invalid');
	}
	
	public function testSignupExists() {
		\Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'qwqw',
		]);

		$before = \Notejam\Entity\User::count();
		
		$this->createBrowser()->post('signup', [
			'user' => [
				'email' => 'test@test.com',
				'password' => 'test',
				'confirm' => 'test'
			]
		]);

		$after = \Notejam\Entity\User::count();
		$this->assertEquals($before, $after, 'user can\'t sign up if email already exists');
	}
	
	public function testSignupDontMatch() {
		$before = \Notejam\Entity\User::count();
		
		$this->createBrowser()->post('signup', [
			'user' => [
				'email' => 'test@test.com',
				'password' => 'test',
				'confirm' => 'test2'
			]
		]);

		$after = \Notejam\Entity\User::count();
		$this->assertEquals($before, $after, 'user can\'t sign up if passwords do not match');
	}
	
	#SIGN IN
	public function testSigninSuccess() {
		\Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$browser = $this->createBrowser();
		$browser->post('signin', [
			'email' => 'test@test.com',
			'password' => 'test',
		]);
		$this->assertEquals(1, $browser->getSession()->get('user'), 'user can successfully sign in');
	}
	
	public function testSigninRequired() {
		\Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$browser = $this->createBrowser();
		$browser->post('signin', [
			'email' => 'test@test.com',
			'password' => '',
		]);
		$this->assertNotInstanceOf('Notejam\Entity\User', $browser->getSession()->get('user'), 'user can\'t sign in if required fields are missing');
	}
	
	public function testSigninWrong() {
		\Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$browser = $this->createBrowser();
		$browser->post('signin', [
			'email' => 'test@test.com',
			'password' => 'qwqw',
		]);
		$this->assertNotInstanceOf('Notejam\Entity\User', $browser->getSession()->get('user'), 'user can\'t sign in if credentials are wrong');

	}
	
	#NOTES
	public function testNoteCreateSuccess() {
		$this->user = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Note::count();

		$this->login()->post('notes/create', [
			'note' => [
				'name' => 'foo',
				'text' => 'bar',
			]
		]);

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before+1, $after, 'note can be successfully created');
	}
	
	public function testNoteCreateAnonymous() {
		$before = \Notejam\Entity\Note::count();

		$this->createBrowser()->post('notes/create', [
			'note' => [
				'name' => 'foo',
				'text' => 'bar',
			]
		]);

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before, $after, 'note can\'t be created by anonymous user');
	}
	
	public function testNoteCreateRequired() {
		$this->user = $user1 = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Note::count();

		$this->createBrowser()->post('notes/create', [
			'note' => [
				'name' => 'foo',
				'text' => '',
			]
		]);

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before, $after, 'note can\'t be created if required fields are missing');
	}
	
	public function testNoteEditOwner() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);

		$this->login()->post('notes/1/edit', [
			'note' => [
				'name' => 'foo',
				'text' => 'bar',
			]
		]);

		$after = \Notejam\Entity\Note::load(1)->name;
		$this->assertEquals('foo', $after, 'note can be edited by its owner');
	}
	
	public function testNoteEditRequired() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);

		$this->login()->post('notes/1/edit', [
			'note' => [
				'name' => 'foo',
				'text' => '',
			]
		]);

		$after = \Notejam\Entity\Note::load(1)->name;
		$this->assertEquals('note1', $after, 'note can\'t be edited if required fields are missing');
	}
	
	public function testNoteEditNotOwner() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$this->login()->post('notes/1/edit', [
			'note' => [
				'name' => 'foo',
				'text' => 'bar',
			]
		]);

		$after = \Notejam\Entity\Note::load(1)->name;
		$this->assertEquals('note1', $after, 'note can\'t be edited by not an owner');
	}
	
	public function testNoteOther() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Note::count();

		$this->login()->post('notes/create', [
			'note' => [
				'name' => 'foo',
				'text' => 'bar',
				'pad' => '1',
			]
		]);

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before, $after, 'note can\'t be added into another\'s user pad');
	}
	
	public function testNoteViewOwner() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);

		$this->assertTrue($this->login()->get('notes/1')->isOK(), 'note can be viewed by its owner');
	}
	
	public function testNoteViewOther() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$this->assertFalse($this->login()->get('notes/1')->isOK(), 'note can\'t be viewed by not an owner');
	}
	
	public function testNoteDeleteOwner() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);

		$before = \Notejam\Entity\Note::count();

		$this->login()->post('notes/1/delete');

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before-1, $after, 'note can be deleted by its owner');
	}
	
	public function testNoteDeleteNotOwner() {
		$note = \Notejam\Entity\Note::create([
			'id' => 1,
			'name' => 'note1',
			'text' => 'text1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'notes' => [
				$note
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Note::count();

		$this->login()->post('notes/1/delete');

		$after = \Notejam\Entity\Note::count();
		$this->assertEquals($before, $after, 'note can\'t be deleted by not an owner');
	}
	
	#PADS
	public function testPadCreateSuccess() {
		$this->user = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Pad::count();

		$this->login()->post('pads/create', [
			'pad' => [
				'name' => 'foo',
			]
		]);

		$after = \Notejam\Entity\Pad::count();
		$this->assertEquals($before+1, $after, 'pad can be successfully created');
	}
	
	public function testPadCreateRequired() {
		$this->user = $user1 = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Pad::count();

		$this->createBrowser()->post('pads/create', [
			'pad' => [
				'name' => '',
			]
		]);

		$after = \Notejam\Entity\Pad::count();
		$this->assertEquals($before, $after, 'pad can\'t be created if required fields are missing');
	}
	
	public function testPadEditOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);

		$this->login()->post('pads/1/edit', [
			'pad' => [
				'name' => 'foo',
			]
		]);

		$after = \Notejam\Entity\Pad::load(1)->name;
		$this->assertEquals('foo', $after, 'pad can be edited by its owner');
	}
	
	public function testPadEditRequired() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);

		$this->login()->post('pads/1/edit', [
			'pad' => [
				'name' => '',
			]
		]);

		$after = \Notejam\Entity\Pad::load(1)->name;
		$this->assertEquals('pad1', $after, 'pad can\'t be edited if required fields are missing');
	}
	
	public function testPadEditNotOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$this->login()->post('pads/1/edit', [
			'pad' => [
				'name' => 'foo',
			]
		]);

		$after = \Notejam\Entity\Pad::load(1)->name;
		$this->assertEquals('pad1', $after, 'pad can\'t be edited by not an owner');
	}
	
	public function testPadViewOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);

		$this->assertTrue($this->login()->get('pads/1')->isOK(), 'pad can be viewed by its owner');
	}
	
	public function testPadViewNotOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$this->assertFalse($this->login()->get('pads/1')->isOK(), 'pad can\'t be viewed by not an owner');
	}
	
	public function testPadDeleteOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);

		$before = \Notejam\Entity\Pad::count();

		$this->login()->post('pads/1/delete');

		$after = \Notejam\Entity\Pad::count();
		$this->assertEquals($before-1, $after, 'pad can be deleted by its owner');
	}
	
	public function testPadDeleteNotOwner() {
		$pad = \Notejam\Entity\Pad::create([
			'id' => 1,
			'name' => 'pad1',
		]);
		\Notejam\Entity\User::create([
			'email' => 'bob@test.com',
			'password' => 'test',
			'pads' => [
				$pad
			]
		]);
		$this->user  = \Notejam\Entity\User::create([
			'email' => 'test@test.com',
			'password' => 'test',
		]);

		$before = \Notejam\Entity\Pad::count();

		$this->login()->post('pads/1/delete');

		$after = \Notejam\Entity\Pad::count();
		$this->assertEquals($before, $after, 'pad can\'t be deleted by not an owner');
	}
}