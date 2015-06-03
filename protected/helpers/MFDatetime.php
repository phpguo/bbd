<?php
class MFDatetime {
    /**
     * @method 获取时间戳毫秒
     */
    public static function microtime() {
        return (int)(microtime(true) * 1000);
    }
}
