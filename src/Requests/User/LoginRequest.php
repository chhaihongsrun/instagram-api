<?php

namespace Instagram\SDK\Requests\User;

use Instagram\SDK\DTO\Messages\User\SessionMessage;
use Instagram\SDK\Http\RequestClient as HttpClient;
use Instagram\SDK\Requests\GenericRequest;
use Instagram\SDK\Requests\Http\Factories\SerializerFactory;
use Instagram\SDK\Requests\Request;
use Instagram\SDK\Requests\Support\SignatureSupport;
use Instagram\SDK\Requests\Traits\RequestMethods;
use Instagram\SDK\Requests\User\Builders\LoginRequestBuilder;
use Instagram\SDK\Responses\Serializers\User\LoginSerializer;
use Instagram\SDK\Session\Session;
use Instagram\SDK\Support\Promise;
use function Instagram\SDK\Support\request;
use function Instagram\SDK\Support\requestWithSerializer;

/**
 * Class LoginRequest
 *
 * @package Instagram\SDK\Requests\User
 */
class LoginRequest extends Request
{

    use RequestMethods;

    /**
     * @var string The login request URI
     */
    protected const REQUEST_URI = 'accounts/login/';

    /**
     * @var string The username
     */
    protected $username;

    /**
     * @var string The password
     */
    protected $password;

    /**
     * LoginRequest constructor.
     *
     * @param string     $username
     * @param string     $password
     * @param Session    $session
     * @param HttpClient $client
     */
    public function __construct(string $username, string $password, Session $session, HttpClient $client)
    {
        $this->username = $username;
        $this->password = $password;

        parent::__construct($session, $client);
    }

    /**
     * Fire the request.
     *
     * @return Promise<SessionMessage>
     */
    public function fire(): Promise
    {
        /** @var GenericRequest $request */
        // phpcs:ignore
        $request = requestWithSerializer(new LoginSerializer($this->session, $this->httpClient), self::REQUEST_URI, new SessionMessage())(
            $this->session,
            $this->httpClient
        );

        // Prepare the payload
        $body = [
            'username'            => $this->username,
            'password'            => $this->password,
            'login_attempt_count' => '0',
            'device_id'           => $this->session->getDevice()->deviceId(),
        ];

        $request->setPayload($body)
            ->setSerializerType(SerializerFactory::TYPE_SIGNED);

        return $request->fire();
    }
}
