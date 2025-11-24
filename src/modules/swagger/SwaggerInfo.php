<?php
namespace Src\modules\swagger;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: "1.0.0",
    description: "Documentación de la API de TODO List",
    title: "TODO List API"
)]
class SwaggerInfo {}
