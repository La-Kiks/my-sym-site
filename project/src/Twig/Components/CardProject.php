<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class CardProject
{
public string $title;

public string $url;

public string $description;
public string $redirect;
public string $techno;
}
