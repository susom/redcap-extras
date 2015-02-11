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


DEFAULT_URL = 'http://127.0.0.1:8080/redcap/index.php'

def upload_data_dictionary(project_url, local_path, headless=False,
                           username=None, password=None):
    fullpath = os.path.abspath(local_path)

    if headless:
        driver = webdriver.PhantomJS()
        driver.set_window_size(1024, 768)
    else:
        driver = webdriver.Firefox()
    
    driver = webdriver.PhantomJS()
    driver.set_window_size(1024, 768)

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
    parser.add_argument('-url','--url', help='URL of the REDCap Project',default=DEFAULT_URL)
    parser.add_argument('-data_dictionary','--data_dictionary', help='path of the Data Dictionary')
    parser.add_argument('-s', '--show', action='store_true', default=False,
                        help='run Firefox instead of PhantomJS')
    parser.add_argument('-username', '--username', help='Enter username',
                        default=None)
    parser.add_argument('-password', '--password', help='Enter password',
                        default=None)

    args = vars(parser.parse_args())

    username = args['username']
    password = args['password']

    upload_data_dictionary(args['url'], args['data_dictionary'],
                           not args['show'], username, password)


if __name__ == '__main__':
    main()

