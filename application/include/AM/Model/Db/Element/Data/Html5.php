<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Html5 class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_Element_Data_Html5 extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_HTML5_BODY                = 'html5_body';
    const DATA_KEY_HTML5_RSS_LINK            = 'rss_link';
    const DATA_KEY_HTML5_RSS_LINK_NUM        = 'rss_link_number';
    const DATA_KEY_HTML5_GOOGLE_LINK_TO_MAP  = 'google_link_to_map';
    const DATA_KEY_HTML5_FACEBOOK_NAME_PAGE  = 'facebook_name_page';
    const DATA_KEY_HTML5_TWITTER_ACCOUNT     = 'twitter_account';
    const DATA_KEY_HTML5_TWITTER_TWEET_COUNT = 'twitter_tweet_number';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('zip'));

    public static $aBodyList = array('code'  => 'Code',
                             'google_maps'   => 'Google Maps',
                             'rss_feed'      => 'RSS Feed',
                             'facebook_like' => 'Facebook like',
                             'twitter'       => 'Twitter tweets list');

    public static $aFieldList = array('code'  => array(self::DATA_KEY_RESOURCE),
                              'google_maps'   => array(self::DATA_KEY_HTML5_GOOGLE_LINK_TO_MAP),
                              'rss_feed'      => array(self::DATA_KEY_HTML5_RSS_LINK, self::DATA_KEY_HTML5_RSS_LINK_NUM),
                              'facebook_like' => array(self::DATA_KEY_HTML5_FACEBOOK_NAME_PAGE),
                              'twitter'       => array(self::DATA_KEY_HTML5_TWITTER_ACCOUNT, self::DATA_KEY_HTML5_TWITTER_TWEET_COUNT));

    /**
     * Remove old fields
     *
     * @param string $sValue
     * @return string
     */
    protected function _addHtml5Body($sValue)
    {
        $sOldBodyType = $this->getDataValue(self::DATA_KEY_HTML5_BODY);

        if (!$sOldBodyType || !array_key_exists($sOldBodyType, self::$aFieldList)) {
            return $sValue;
        }

        foreach (self::$aFieldList[$sOldBodyType] as $sField) {
            if (self::DATA_KEY_RESOURCE != $sField) {
                $this->delete($sField);
            }
        }

        return $sValue;
    }

    /*
     * @return string
     */
    public function getImageType($sKeyName = self::DATA_KEY_RESOURCE)
    {
        return 'zip';
    }

    /**
     * Magix method to validate RSS URL
     * @param mixed $mValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addRssLink($mValue)
    {
        $mValue     = (string) $mValue;

        if (!Zend_Uri_Http::check($mValue)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given. It must be an valid URL.', self::DATA_KEY_HTML5_RSS_LINK));
        }

        return $mValue;
    }

    /**
     * Magic method to validate amount of RSS records on the page
     * @param mixed $mValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addRssLinkNumber($mValue)
    {
        if (!Zend_Validate::is($mValue, 'int')) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_HTML5_RSS_LINK_NUM));
        }

        return $mValue;
    }

    /**
     * Magic method to validate twitter account name
     * @param mixed $mValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTwitterAccount($mValue)
    {
        $mValue = AM_Tools::filter_xss($mValue);

        if (!Zend_Validate::is($mValue, 'regex', array('pattern' => '/^[A-Za-z0-9_]+$/'))) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_HTML5_TWITTER_ACCOUNT));
        }

        return $mValue;
    }

    /**
     * Magic method to validate amount of Twitter records on the page
     * @param mixed $mValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTwitterTweetNumber($mValue)
    {
        if (!Zend_Validate::is($mValue, 'int')) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_HTML5_TWITTER_TWEET_COUNT));
        }

        return $mValue;
    }

    /**
     * Magic method to validate facebook account name
     * @param mixed $mValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addFacebookNamePage($mValue)
    {
        $mValue = AM_Tools::filter_xss($mValue);

        if (!Zend_Validate::is($mValue, 'regex', array('pattern' => '/^[a-z\d.]{5,}$/i'))) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_HTML5_FACEBOOK_NAME_PAGE));
        }

        return $mValue;
    }

     /**
     * Magic method to validate google map link
     * @param mixed $mValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addGoogleLinkToMap($mValue)
    {
        $mValue = (string) $mValue;

        if (!Zend_Uri_Http::check($mValue)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given. It must be an valid URL.', self::DATA_KEY_HTML5_GOOGLE_LINK_TO_MAP));
        }
        return $mValue;
    }
}