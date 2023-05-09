<?
if(session_status()!==PHP_SESSION_ACTIVE){
    $ENV = parse_ini_file(__DIR__.DIRECTORY_SEPARATOR.'.env');
    foreach ($ENV as $key => $line){putenv($key.'='.$line);}
    $params             = session_get_cookie_params();
    $params['lifetime'] = 0;
    $params['httponly'] = true;
    $params['secure']   = true;
    $params['samesite'] = 'Lax';
    $params['domain']   = $_SERVER['HTTP_HOST']??$_SERVER['SERVER_NAME'];
    session_set_cookie_params($params);
    if(!is_null(getEnv('SESSION_SAVE_PATH')) && getEnv('SESSION_SAVE_PATH')!=""){
        session_save_path(getEnv('SESSION_SAVE_PATH'));
    }
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.name', 'app_session_name');
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.sid_length', 128);
    ini_set('session.auto_start', 0);
    ini_set('url_rewriter.tags', '');
}

