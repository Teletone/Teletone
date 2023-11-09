<?php

namespace Teletone;

/** Types of updates */
class Types
{
    const ANIMATION = 1;
    const AUDIO = 2;
    const DOCUMENT = 4;
    const PHOTO = 8;
    const STICKER = 16;
    const STORY = 32;
    const VIDEO = 64;
    const VIDEONOTE = 128;
    const VOICE = 256;
    const CONTACT = 512;
    const TEXT = 1024;
    const DICE = 2048;
    const GAME = 4096;
    const POLL = 8192;
    const VENUE = 16384;
    const LOCATION = 32768;
    const INVOICE = 65536;
    const PASSPORTDATA = 131072;
    const ALL = self::ANIMATION|self::AUDIO|self::DOCUMENT|self::PHOTO|self::STICKER|self::STORY|self::VIDEO|self::VIDEONOTE|self::VOICE|self::CONTACT|self::TEXT|self::DICE|self::GAME|self::POLL|self::VENUE|self::LOCATION|self::INVOICE|self::PASSPORTDATA;
}