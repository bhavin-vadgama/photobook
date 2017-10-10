<?php

require_once __DIR__ . '../../lib/src/Facebook/autoload.php';
set_time_limit(0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class FacebookClass
{
    protected $app_id;
    protected $app_secret;
    protected $graph_version;
    protected $fb;
    protected $helper;
    protected $access_token;

    public function FacebookClass($app_id = '<YOUR FACEBOOK API CLIENT ID>', $app_secret = '<YOUR FACEBOOK API CLIENT SECRET>', $graph_version = 'v2.5')
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->graph_version = $graph_version;
    }

    public function initializeFB()
    {
        $this->fb = new Facebook\Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => $this->graph_version,
        ]);

        $this->helper = $this->fb->getJavaScriptHelper();

        try {
            $this->accessToken = $this->helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }

    public function initializeFBWithSession()
    {
        $this->fb = new Facebook\Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => $this->graph_version,
        ]);

        $this->fb->setDefaultAccessToken($_SESSION['fb_access_token']);
    }

    public function getAccessToken()
    {
        return (string) $this->access_token;
    }

    public function makeTokenLongLived()
    {
        if (!$this->accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                // The OAuth 2.0 client handler helps us manage access tokens
                $oAuth2Client = $this->fb->getOAuth2Client();
                $accessToken = $oAuth2Client->getLongLivedAccessToken($this->accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Error getting long-lived access token: ' . $this->helper->getMessage();
                exit;
            }
        }
    }

    public function checkForPermissions()
    {
        $perm = $this->getData('/me/permissions');
        $flaf = true;
        if (count($perm['data']) > 0) {
            for ($i = 0; $i < count($perm['data']); ++$i) {
                if (!($perm['data'][$i]['permission'] == 'user_photos' || $perm['data'][$i]['permission'] == 'email' || $perm['data'][$i]['permission'] == 'public_profile')) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function setSessionParams()
    {
        $profile = $this->getData('/me?fields=id,name,email,gender,picture{url}');
        $_SESSION['fb_access_token'] = (string) $this->accessToken;
        $_SESSION['user']['id'] = $profile['id'];
        $_SESSION['user']['name'] = $profile['name'];
        $_SESSION['user']['email'] = $profile['email'];
        $_SESSION['user']['gender'] = $profile['gender'];
        $_SESSION['user']['picture'] = $profile['picture']['data']['url'];
    }

    public function setAccessTokenDefault()
    {
        if (isset($this->accessToken)) {
            $this->fb->setDefaultAccessToken($this->accessToken);
        }
    }

    public function getData($pattern)
    {
        // /me?fields=id,name,email,gender,picture{url}
        try {
            $request = $this->fb->get($pattern);
            $request = $request->getDecodedBody();
            return $request;
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }
}
