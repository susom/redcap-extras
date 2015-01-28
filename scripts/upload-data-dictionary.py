#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import os
import time

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait

def upload_data_dictionary(project_url, local_path, headless=False,
                           username=None, password=None):
    fullpath = os.path.abspath(local_path)

    if headless:
        driver = webdriver.PhantomJS()
        driver.set_window_size(1024, 768)
    else:
        driver = webdriver.Firefox()

    try:
        driver.implicitly_wait(3)
        driver.get(project_url)

        if username:
            print "Logging in..."
            driver.find_element_by_id('username').send_keys(username)
            driver.find_element_by_id('password').send_keys(password)

        driver.find_element_by_link_text("Project Setup").click()
        driver.find_element_by_xpath("//div[@id='setupChklist-design']/table/tbody/tr/td[2]/div[2]/div/button[2]").click()
        driver.find_element_by_name("uploadedfile").send_keys(fullpath)

        time.sleep(0.1)
        driver.find_element_by_id("submit").click()
        time.sleep(0.1)
        assert "Your document was uploaded successfully and awaits your confirmation below." == \
               driver.find_element_by_css_selector("div.darkgreen > b").text
        driver.find_element_by_name("commit").click()
        assert "Changes Made Successfully!" == driver.find_element_by_css_selector("div.green > b").text
    except:
        screenshot = './screenshot-timeout.png'
        driver.save_screenshot(screenshot)
        print "Screenshot of error saved to: ", screenshot
        raise
    finally:
        driver.quit()

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('url', help='URL of the REDCap Project')
    parser.add_argument('data-dictionary', help='path of the Data Dictionary')
    parser.add_argument('-s', '--show', action='store_true', default=False,
                        help='run Firefox instead of PhantomJS')
    parser.add_argument('-c', '--credentials',
                        help='file that only contains the REDCap username and '
                             'password to use, separated by a vertical pipe. '
                             'Use "-" to read from stdin. Example: '
                             '"username|password"',
                        type=argparse.FileType('r'))
    args = vars(parser.parse_args())

    credentials = args['credentials']
    if credentials:
        username, password = credentials.read().split('|')
    else:
        username, password = None, None


    upload_data_dictionary(args['url'], args['data-dictionary'],
                           not args['show'], username, password)


if __name__ == '__main__':
    main()

