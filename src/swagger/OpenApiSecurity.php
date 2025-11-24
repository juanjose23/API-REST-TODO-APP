<?php

namespace Src\swagger;
use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class OpenApiSecurity {}
