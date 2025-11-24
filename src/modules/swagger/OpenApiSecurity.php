<?php

namespace Src\modules\swagger;
use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class OpenApiSecurity {}
