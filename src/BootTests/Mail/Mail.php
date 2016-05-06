<?php

namespace BootTests\Mail;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Message;

use Zend\View\Model\ViewModel;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * Class Mail
 * @package BootTests\Mail
 */
class Mail
{
    /**
     * @var SmtpTransport
     */
    protected $transport;

    /**
     * @var
     */
    protected $view;

    /**
     * @var
     */
    protected $body;

    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $subject;

    /**
     * @var
     */
    protected $to;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $page;

    /**
     * Mail constructor.
     * @param SmtpTransport $transport
     * @param $view
     * @param $page
     */
    public function __construct(SmtpTransport $transport, $view, $page)
    {
        $this->transport = $transport;
        $this->view = $view;
        $this->page = $page;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $page
     * @param array $data
     * @return mixed
     */
    public function renderView($page, array $data)
    {
        $model = new ViewModel;
        $model->setTemplate("mailer/{$page}.phtml");
        $model->setOption('has_parent',true);
        $model->setVariables($data);

        return $this->view->render($model);
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        $html = new MimePart($this->renderView($this->page, $this->data));
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));
        $this->body = $body;

        $config = $this->transport->getOptions()->toArray();

        $this->message = new Message;
        $this->message->addFrom($config['connection_config']['from'])
                ->addTo($this->to)
                ->setSubject($this->subject)
                ->setBody($this->body);

        return $this;
    }

    public function send()
    {
        $this->transport->send($this->message);
    }
}
