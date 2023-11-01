<?php

namespace Teletone;

/** Chat member statuses */
class Statuses
{
    const CREATOR = 1;
    const ADMINISTRATOR = 2;
    const MEMBER = 4;
    const RESTRICTED = 8;
    const LEFT = 16;
    const KICKED = 32;
}