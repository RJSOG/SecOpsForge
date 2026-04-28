<?php

namespace App\Enum;

enum EventEnum: string
{
    case BUILD_PAGE = 'build_page';
    case BUILD_FILE_TREE = 'build_file_tree';
    case VALIDATE_FILE_TREE = 'validate_file_tree';
    case PAGE_RENDERING = 'page_rendering';
}
