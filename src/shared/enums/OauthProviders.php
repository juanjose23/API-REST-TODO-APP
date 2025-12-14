<?php

namespace Src\shared\enums;

enum OauthProviders :string
{
    case GOOGLE = 'google';
    case FACEBOOK = 'facebook';
    case GITHUB = 'github';
    case APPLE = 'apple';
    case TWITTER = 'twitter';
}
