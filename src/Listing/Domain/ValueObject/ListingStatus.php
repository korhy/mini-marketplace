<?php

declare(strict_types=1);

namespace App\Listing\Domain\ValueObject;

enum ListingStatus: string
{                                                                                                  
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SOLD = 'sold';          
}
